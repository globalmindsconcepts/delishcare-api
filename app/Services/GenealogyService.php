<?php
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Repositories\ReferralBonusRepository;
use App\Repositories\SettingRepository;
use App\Repositories\ReferralBonusSettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\PackageRepository;
use App\Repositories\PlacementBonusRepository;
use Illuminate\Support\Facades\Log;

Class GenealogyService {

    public $childrenTable;
    private $referralBonus;
    private $setting;
    private $referralBonusSetting,$user,$package, $placementBonus;

    public function __construct()
    {
        $this->childrenTable = DB::table('children');
        $this->referralBonus = new ReferralBonusRepository;
        $this->setting = new SettingRepository;
        $this->referralBonusSetting = new ReferralBonusSettingRepository;
        $this->user = new UserRepository;
        $this->package = new PackageRepository;
        $this->placementBonus = new PlacementBonusRepository;
    }
    /**
     * make user (it could be a referrer) a parent
     */
    public function makeReferrerAParent($ref_id,$child_id,$placer_id=null): void
    {
        $query_1 = DB::table('children')->select('id')->where('parent_id',$ref_id)->get(); //Children::where('parent_id',$ref_id)->get(); //"select parent_id from children where parent_id=$ref_id and level_id=$level_id";
        if($query_1->count() < config('genealogy.max_referrals') && !$this->checkDuplicateChildId($child_id)) {
            $this->makeReferredAChild($ref_id,$child_id,$placer_id); 
            
            //generation 1
        }
        
        if($query_1->count() >= config('genealogy.max_referrals') && !$this->checkDuplicateChildId($child_id)) {
            
            $children = $this->findChildren($ref_id);
            if($children != null){
                info('gen',[$query_1->count()]);
                //generation 2
                $this->makeReferredAGrandChild($ref_id,$child_id,$children);
                //if($level_id==config('level_start')){
                    $grandchildren = $this->findGrandChildren($ref_id);
                    if(! is_null($grandchildren)){
                        $this->makeReferredAGreatGrandChild($ref_id,$child_id,$grandchildren);
                    }
                //}
            } 
        }
    }

    private function makeReferredAChild($ref_id,$child_id,$placer_id=null):void
    {
        DB::table('children')->insert([
            'child_id'=>$child_id,
            'parent_id'=>$ref_id,
            //'level_id'=>$level_id
        ]);//"insert into children (child_id,parent_id,level_id) values('$child_id','$ref_id','$level_id')");

        $data = null;
        if(!$placer_id){
            $user = $this->user->getUser($child_id);
            $ref = $this->user->getUser($ref_id);
                
            if($user && $ref){
                $package_id = $user['package_id'];
                $package_pv = $this->package->get($package_id)->point_value;
                $unit_pv = $this->setting->get('unit_point_value')->unit_point_value;
            
                if($refBonus = $this->referralBonusSetting->get($ref['package_id'])){
                    info('ref-set', [$unit_pv]);
                    $generation_1_percentage = $refBonus->generation_1_percentage;
                    $bonus = ($package_pv * $generation_1_percentage/100) * $unit_pv;
                    
                    $this->referralBonus->create([
                        'user_uuid'=>$ref_id,
                        'generation'=>'generation_1',
                        'bonus'=>$bonus,
                        'data'=> json_encode([
                            'percentage'=>$generation_1_percentage,
                            'unit_pv'=>$unit_pv,
                            'package_id'=>$package_id,
                            'package_pv'=>$package_pv
                        ])
                    ]);

                    $data = [
                        'ref_bonus'=>$refBonus,
                        'unit_pv'=>$unit_pv,
                        'package_id'=>$package_id,
                        'package_pv'=>$package_pv
                    ];
                }

                $gen_4_count = $this->referralBonus->table->where(['user_uuid' => $ref_id, 'generation' => 'generation_4'])->count();
                $gen_5_count = $this->referralBonus->table->where(['user_uuid' => $ref_id, 'generation' => 'generation_5'])->count();

                if($refBonus->generation_5_percentage>0 && ($gen_4_count==16 || $gen_5_count<32)){
                    $generation_percentage = $refBonus->generation_5_percentage;
                    $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                    $generation = 'generation_5';
                }
                
                elseif($refBonus->generation_6_percentage>0 && $gen_5_count==32){
                    $generation_percentage = $refBonus->generation_6_percentage;
                    $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                    $generation = 'generation_6';
                }
            }
        }else{
            $user = $this->user->getUser($child_id);
            $placer = $this->user->getUser($placer_id);

            if($user && $placer){
                $package_id = $user['package_id'];
                $package_pv = $this->package->get($package_id)->point_value;
                $unit_pv = $this->setting->get('unit_point_value')->unit_point_value;
                $placement_bonus = $this->setting->get('placement_bonus_percentage')->placement_bonus_percentage;

                $bonus = ($package_pv * $placement_bonus/100) * $unit_pv;

                $this->placementBonus->create([
                    'user_uuid' => $placer_id,
                    'bonus' => $bonus,
                    'data' => json_encode([
                        'unit_pv' => $unit_pv,
                        'placement_bonus_percentage' => $placement_bonus,
                        'package_id'=>$package_id,
                        'package_pv' => $package_pv,
                        'downline_uuid' => $child_id
                    ])
                ]);

                // $sum = 0;
                // $gen_1_count = $this->referralBonus->table->where(['user_uuid' => $placer_id, 'generation' => 'generation_1'])->count();
                // $gen_2_count = $this->referralBonus->table->where(['user_uuid' => $placer_id, 'generation' => 'generation_2'])->count();
                // $gen_3_count = $this->referralBonus->table->where(['user_uuid' => $placer_id, 'generation' => 'generation_3'])->count();
                // $gen_4_count = $this->referralBonus->table->where(['user_uuid' => $placer_id, 'generation' => 'generation_4'])->count();
                // $gen_5_count = $this->referralBonus->table->where(['user_uuid' => $placer_id, 'generation' => 'generation_5'])->count();
                // $gen_6_count = $this->referralBonus->table->where(['user_uuid' => $placer_id, 'generation' => 'generation_6'])->count();
                // $countDownlines = $this->sumDownlines($sum, $package_id);

                
                // if($refBonus = $this->referralBonusSetting->get($placer['package_id'])){
                //     if($refBonus->generation_1_percentage>0 && $gen_1_count<2){
                //         $generation_percentage = $refBonus->generation_1_percentage;
                //         $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                //         $generation = 'generation_1';
                //     }

                //     elseif($refBonus->generation_2_percentage>0 && ($gen_1_count==2 || $gen_2_count<4)){
                //         $generation_percentage = $refBonus->generation_2_percentage;
                //         $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                //         $generation = 'generation_2';
                //     }
                    
                //     elseif($refBonus->generation_3_percentage>0 && ($gen_2_count==4 || $gen_3_count<8)){
                //         $generation_percentage = $refBonus->generation_3_percentage;
                //         $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                //         $generation = 'generation_3';
                //     }
                
                //     elseif($refBonus->generation_4_percentage>0 && ($gen_3_count==8 || $gen_4_count<16)){
                //         $generation_percentage = $refBonus->generation_4_percentage;
                //         $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                //         $generation = 'generation_4';
                //     }
                    
                //     elseif($refBonus->generation_5_percentage>0 && ($gen_4_count==16 || $gen_5_count<32)){
                //         $generation_percentage = $refBonus->generation_5_percentage;
                //         $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                //         $generation = 'generation_5';
                //     }
                    
                //     elseif($refBonus->generation_6_percentage>0 && $gen_5_count==32){
                //         $generation_percentage = $refBonus->generation_6_percentage;
                //         $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                //         $generation = 'generation_6';
                //     }

                //     $this->referralBonus->create([
                //         'user_uuid'=>$placer_id,
                //         'generation'=>$generation,
                //         'bonus'=>$bonus,
                //         'data'=> json_encode([
                //             'percentage'=>$generation_percentage,
                //             'unit_pv'=>$unit_pv,
                //             'package_id'=>$package_id,
                //             'package_pv'=>$package_pv
                //         ])
                //     ]);
                // }
            }
        }
        
        if(!is_null($this->checkIfRefHasAParent($ref_id))){
            $grandparent_id = $this->checkIfRefHasAParent($ref_id);
            $parent_id = $ref_id;
            $grandchild_id = $child_id;
            // make the registrant// a grand child and make the ref a grand parent
            $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id,$data); 
            //make registrant a great_grandchild
        }
        //if($level_id == config('level.level_start')){
            if(!is_null($this->checkIfRefHasAGrandParent($ref_id))){
                $grandparent_id = $this->checkIfRefHasAGrandParent($ref_id);
                $great_grandparent_id = null;
                if(!is_null($this->checkIfRefHasAParent($grandparent_id))){
                    $great_grandparent_id = $this->checkIfRefHasAParent($grandparent_id);
                    $great_grandchild_id = $grandchild_id;
                    $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id, $data);
                } 
                
                if($gen_4_id = $this->checkIfRefHasAParent($great_grandparent_id)){
                    info('gen_4', [$gen_4_id]);
                    $gen_3_count = $this->referralBonus->table->where(['user_uuid' => $great_grandparent_id, 'generation' => 'generation_3'])->count();
                    $gen_4_count = $this->referralBonus->table->where(['user_uuid' => $gen_4_id, 'generation' => 'generation_4'])->count();
                    if($refBonus->generation_4_percentage>0 && ($gen_3_count==8 || $gen_4_count<16)){
                        $generation_percentage = $refBonus->generation_4_percentage;
                        $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                        $generation = 'generation_4';

                        $this->referralBonus->create([
                            'user_uuid'=>$gen_4_id,
                            'generation'=>$generation,
                            'bonus'=>$bonus,
                            'data'=> json_encode([
                                'percentage'=>$generation_percentage,
                                'unit_pv'=>$unit_pv,
                                'package_id'=>$package_id,
                                'package_pv'=>$package_pv
                            ])
                        ]);
                    }
                    
                }

                if($gen_5_id = $this->checkIfRefHasAParent($gen_4_id)){
                info('gen_5', [$gen_5_id]);
                    $gen_5_count = $this->referralBonus->table->where(['user_uuid' => $gen_5_id, 'generation' => 'generation_5'])->count();
                    if($refBonus->generation_5_percentage>0 && ($gen_4_count==16 || $gen_5_count<32)){
                        $generation_percentage = $refBonus->generation_5_percentage;
                        $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                        $generation = 'generation_5';

                        $this->referralBonus->create([
                            'user_uuid'=>$gen_5_id,
                            'generation'=>$generation,
                            'bonus'=>$bonus,
                            'data'=> json_encode([
                                'percentage'=>$generation_percentage,
                                'unit_pv'=>$unit_pv,
                                'package_id'=>$package_id,
                                'package_pv'=>$package_pv
                            ])
                        ]);
                    }
                }

                if($gen_6_id = $this->checkIfRefHasAParent($gen_5_id)){
                    $gen_6_count = $this->referralBonus->table->where(['user_uuid' => $gen_6_id, 'generation' => 'generation_6'])->count();
                    if($refBonus->generation_6_percentage>0 && ($gen_5_count==32 || $gen_6_count<64)){
                        $generation_percentage = $refBonus->generation_6_percentage;
                        $bonus = ($package_pv * $generation_percentage/100) * $unit_pv;
                        $generation = 'generation_5';

                        $this->referralBonus->create([
                            'user_uuid'=>$gen_6_id,
                            'generation'=>$generation,
                            'bonus'=>$bonus,
                            'data'=> json_encode([
                                'percentage'=>$generation_percentage,
                                'unit_pv'=>$unit_pv,
                                'package_id'=>$package_id,
                                'package_pv'=>$package_pv
                            ])
                        ]);
                    }
                }
            }
        //} 
    }

    private function makeReferredAGrandChild($ref_id,$child_id,$children):void
    {
        foreach ($children as $value){
            $value = $value->child_id;
            $query_2 = DB::table('children')->select('parent_id')->where(['parent_id'=>$value])->get();
            //info('gen-2',[$query_2->count()]);
            if($query_2->count() < config('genealogy.max_referrals') && !$this->checkDuplicateChildId($child_id)) {
                DB::table('children')->insert([
                    'child_id'=>$child_id,
                    'parent_id'=>$value,
                    //'level_id'=>$level_id
                ]); //("insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')"); //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                //generation2
                $grandchild_id = $child_id;
                $parent_id = $value;
                $grandparent_id = $ref_id;
                $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id);
                //if($level_id==0){
                    if(!is_null($this->checkIfRefHasAGrandParent($ref_id))){
                        $great_grandparent_id = $this->checkIfRefHasAGrandParent($ref_id);
                        $great_grandchild_id = $grandchild_id;
                        $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id);     
                    }
                //}
                break;
            }
        }
    }

    private function makeReferredAGreatGrandChild($ref_id,$child_id,$grandchildren):void
    {
        foreach ($grandchildren as $value){
            $value = $value->grandchild_id;
            $query = DB::table('children')->select('parent_id')->where(['parent_id'=>$value])->get();//"select parent_id from children where parent_id=$value and level_id=$level_id";
            if($query->count() < config('level.max_referrals') && !$this->checkDuplicateChildId($child_id)) {
                $query = DB::table('children')->insert([
                    'child_id'=>$child_id,
                    'parent_id'=>$value,
                    //'level_id'=>$level_id
                ]);
                //generation 3
                 //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                if(!is_null($this->checkIfRefHasAParent($value)) ){
                    $grandparent_id = $this->checkIfRefHasAParent($value);
                    $parent_id = $value;
                    $grandchild_id = $child_id;
                    // make the registrant// a grand child and make the ref a grand parent
                    $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id); 
                    //make registrant a great_grandchild
                }
                $great_grandchild_id = $child_id;
                $parent_id = $value;
                $great_grandparent_id = $ref_id;
                $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id);
                break;
            }
        }
        $great_grandchildren = $this->findGreatGrandChildren($ref_id);
        if(!is_null($great_grandchildren)){
            foreach ($great_grandchildren as $value){
                $value = $value->great_grandchild_id;
                $query = DB::table('children')->select('parent_id')->where(['parent_id'=>$value])->get();
                if($query->count() < config('level.max_referrals') && !$this->checkDuplicateChildId($child_id)) {
                    $query = DB::table('children')->insert([
                        'child_id'=>$child_id,
                        'parent_id'=>$value,
                        //'level_id'=>$level_id
                    ]); 
                    //generation4
                    //"insert into children (child_id,parent_id,level_id) values('$child_id','$value','$level_id')";
                    if(!is_null($this->checkIfRefHasAParent($value)) ){
                        $grandparent_id = $this->checkIfRefHasAParent($value);
                        $parent_id = $value;
                        $grandchild_id = $child_id;
                        // make the registrant// a grand child and make the ref a grand parent
                        $this->makeUserAGrandParent($grandchild_id, $parent_id, $grandparent_id); 
                        //make registrant a great_grandchild
                        if($this->checkIfRefHasAParent($grandparent_id)){
                            $great_grandchild_id = $child_id;
                            $parent_id = $value;
                            $great_grandparent_id = $this->checkIfRefHasAParent( $grandparent_id);
                            $this->makeUserAGreatGrandParent($great_grandchild_id, $parent_id, $great_grandparent_id);
                        }
                    }
                    break;
                }
            }
        }
    }

    /**
     * make user  a greeat grand parent
     */
    private function makeUserAGrandParent($grandchild_id,$parent_id,$grandparent_id,$ref_bonus_data=null) {
        $query = DB::table('grandchildren')->insert([
            'grandchild_id'=>$grandchild_id,
            'parent_id'=>$parent_id,
            'grandparent_id'=>$grandparent_id,
            //'level_id'=>$level_id
        ]);

        if($ref_bonus_data){
            $this->referralBonus->create([
                'user_uuid'=>$grandparent_id,
                'generation'=>'generation_2',
                'bonus'=> ($ref_bonus_data['package_pv'] * $ref_bonus_data['ref_bonus']->generation_2_percentage/100) * $ref_bonus_data['unit_pv'],
                'data'=> json_encode([
                    'percentage'=>$ref_bonus_data['ref_bonus']->generation_2_percentage,
                    'unit_pv'=>$ref_bonus_data['unit_pv'],
                    'package_id'=>$ref_bonus_data['package_id'],
                    'package_pv'=>$ref_bonus_data['package_pv']
                ])
            ]);
        }
    }

    /**
     * make user a greartgrand parent
     */
    private function makeUserAGreatGrandParent($great_grandchild_id,$parent_id,$great_grandparent_id,$ref_bonus_data=null) {
        DB::table('great_grandchildren')->insert([
            'great_grandchild_id'=>$great_grandchild_id,
            'parent_id'=>$parent_id,
            'great_grandparent_id'=>$great_grandparent_id,
            //'level_id'=>$level_id
        ]);

        if($ref_bonus_data){
            $this->referralBonus->create([
                'user_uuid'=>$great_grandparent_id,
                'generation'=>'generation_3',
                'bonus'=> ($ref_bonus_data['package_pv'] * $ref_bonus_data['ref_bonus']->generation_3_percentage/100) * $ref_bonus_data['unit_pv'],
                'data'=> json_encode([
                    'percentage'=>$ref_bonus_data['ref_bonus']->generation_3_percentage,
                    'unit_pv'=>$ref_bonus_data['unit_pv'],
                    'package_id'=>$ref_bonus_data['package_id'],
                    'package_pv'=>$ref_bonus_data['package_pv']
                ])
            ]);
        }
    }

    /**
     * check if the referral has a parent
     * can also be used to check if user has a parent
     * @return - parent_id
     */
    public function checkIfRefHasAParent($ref_id){
        //if(!is_null($level_id)){
        $res = DB::table('children')->select('parent_id')->where('child_id',$ref_id)->first(); 
        //Children::where('child_id',$ref_id)->value('parent_id'); //"select parent_id from children where child_id=$ref_id limit 1";
        if(!is_null($res)){
            return $res->parent_id;
        } else {
            return null;
        }
    }

    /**
     * check if the referral has a grandparent
     * @return - grandparent_id
     */
    private function checkIfRefHasAGrandParent($ref_id){
        $res = DB::table('grandchildren')->where('parent_id',$ref_id)->first(); //"select grandparent_id from grandchildren where parent_id=$ref_id limit 1";
        if(!is_null($res)){
            return $res->grandparent_id;
        } else {
            return null;
        }
    }

    /**
     * check for duplicate child
     * @return - boolean
     */
    public function checkDuplicateChildId($child_id) {
        $res = DB::table('children')->select('id')->where(['child_id'=>$child_id])->first(); //"select child_id from children where child_id = $child_id and level_id=$level_id";
        if(!is_null($res)){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * find grandchildren
     * @return - grandchild_id
     */
    private function findGrandChildren($ref_id) {
        $res = DB::table('grandchildren')->select('grandchild_id')->where(['grandparent_id'=>$ref_id])->get();// "select grandchild_id from grandchildren where grandparent_id=$ref_id and level_id=$level_id";
        if($res->count() > 0){
        return $res;
        }
        return null; 
        
    }

    public function findGreatGrandChildren($ref_id){
        $res = DB::table('great_grandchildren')->select('great_grandchild_id')->where(['great_grandparent_id'=>$ref_id])->get();// "select grandchild_id from grandchildren where grandparent_id=$ref_id and level_id=$level_id";
        if($res->count() > 0){
        return $res;
        }
        return null; 
    }

    /**
     * find children
     * @return - child_id
     */
    public function findChildren($ref_id,$check_for_level_movement=false) {
        $res = DB::table('children')->select('child_id')->where(['parent_id'=>$ref_id])->orderBy('id','desc')->get();//"select child_id from children where parent_id='$ref_id' and level_id='$level_id'";
        if($check_for_level_movement){
            if($res->count() == 2){ // change back to 2
                return $res;
            }
                return null;
        }else{
            if($res->count() > 0){
                return $res;
            }
                return null;
        }
    }


    /**
    * get level geneology structure
    */  
    public function getLevelTreeStructure($user_id,$history=null,$id=null)
    {
        if($history && User::find($id)){
            $level_id = config('level.level_end');//User::findOrFail($user_id)->level_id;
            $family_arr = [
                'name' => User::find($id)->username,
                'package' => 'Stage 6',
                'img'=> User::find($id)->user_img
            ];

            return $this->generateTree($family_arr,$user_id,$level_id);
            
        }else{
            if(User::where('uuid',$user_id)->first()){
                $level_id = User::where('uuid',$user_id)->first()->level_id;//User::findOrFail($user_id)->level_id;
               
                $family_arr = [
                    'name'=> User::where('uuid',$user_id)->first()->username,
                    'package' => User::where('uuid',$user_id)->first()->package->name,
                    'img'=> User::where('uuid',$user_id)->first()->profile->user_img
                ];  // parent

               return $this->generateTree($family_arr,$user_id,$level_id);
            }
        }   
    }

    /**
     * generate geneaology tree recursively
     * @$family_arr - genealogy array passed by reference
     * @child_id - child id used in traversing
     * @level_id  - level of child 
     */
    public function generateTree(&$family_arr,$child_id): array
    {
        if($children = $this->findChildren($child_id,false)){
            foreach($children as $child){
                $family_arr['children'][] = [
                    "name" => User::where('uuid',$child->child_id)->first()->username,
                    "title" => USer::where('uuid',$child->child_id)->first()->package->name,
                    'img'=> User::where('uuid',$child->child_id)->first()->profile->photo_path
                ];

               //if($this->findChildren($child->child_id,false)){
                    $this->generateTree($family_arr['children'][count($family_arr['children'])-1],$child->child_id);
                    //$this->getDownlineTreeStructure($child->child_id);
               //} 
            }
        }
        return $family_arr;
    }

    

    /**
    * get downline geneology structure
    */
    public function getDownlineTreeStructure($user_id,$history=null,$id=null){
        
        if(User::where('uuid',$user_id)->first()){
            $family_arr = [
                'name' => User::where('uuid',$user_id)->first()->username,
                'title' => User::where('uuid',$user_id)->first()->package->name,
                //'img'=> User::where('uuid',$user_id)->first()->profile->photo_path
            ];
            $arr = $this->generateTree($family_arr,$user_id);
            info('gen',[$arr]);
            return $arr ;
        }
    }

    public function sumDownlines(&$sum,$uuid)
    {
        $children = $this->findChildren($uuid,false);
        if(!is_null($children)){
            $sum += count($children);
            foreach($children as $child){
                $this->sumDownlines($sum,$child->child_id);
            }
         }
         return $sum;
    }

    public function sumUserPointValue(string $uuid, int &$sum)
    {
        $user = $this->user->getUser($uuid);
        $package_id = $user['package_id'];
        $package_pv = $this->package->get($package_id)->point_value;
        $sum = $sum + $package_pv;

        $children = $this->findChildren($uuid,false);
        if(!is_null($children)){
            foreach($children as $child){
                $this->sumDownlines($sum,$child->child_id);
            }
         }
         return $sum;
    }

    /**
     * total downlines at each level
     */
    public function totalLevelDownlines($user,$level=null)
    {
        $level_id = !is_null($level)? $level : $user->level_id;
        $children_array = [];
        $grand_children_array = [];
        $total_downlines = 0;

        return $this->sumDownlines($total_downlines,$user->uuid);
        // $children = $this->findChildren($user->uuid, $level_id,false);
        // if(!is_null($children)){ //check if user has children
        //     //$array = [];
        //     foreach($children as $child){//loop trough the children and push their id into a stack
        //         array_push($children_array,$child->child_id);
                
        //         $grandchildren = $this->findChildren($child->child_id, $level_id,false);
        //         if(!is_null($grandchildren)){//if user has grandchildren
        //             foreach($grandchildren as $child1){//loop trough the grandchildren and push their id into a stack
        //             array_push($grand_children_array,$child1->child_id);
        //             }
        //         }
        //     }
        // }
        // $total_downlines =  count($children_array) + count($grand_children_array);
        // return $total_downlines;
    }
}
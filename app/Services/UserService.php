<?php
namespace App\Services;

//use App\Interfaces\UserInterface;
use App\Models\Package;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\UserConfirmationEmail;
use Illuminate\Support\Facades\Mail;
use App\Repositories\ReferralRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\PackageRepository;
use App\Repositories\PackagePaymentRepository;
use App\Mail\InviteGuestMail;
//se Illuminate\Support\Facades\Mail;

class UserService extends BaseService{

    private $service;
    private $userRepository;
    private $referralRepository;
    private $packageRepo;
    private $genealogyService;
    private $packagePaymentRepo;

    function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->referralRepository = new ReferralRepository;
        $this->packageRepo = new PackageRepository;
        $this->packagePaymentRepo = new PackagePaymentRepository;
    }

    public function all(bool $count=false)
    {
        try {
            $data = $this->userRepository->all($count);
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching users");
        }

        return ["success"=>true, "data"=>$data,
        "message"=>$count ? 'Total users': 'Users'."fetched sucessfully","status"=>200];
    }
    
    public function activeUsers(bool $count=false)
    {
        try {
            $data = $this->userRepository->activeUsers($count);
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching active users");
        }

        return ["success"=>true, "data"=>$data,
        "message"=>$count ? 'Total active users': 'Users'."fetched sucessfully","status"=>200];
    }

    public function create(array $data)
    {
        try {
            
            $referer = $this->userRepository->getUser($data['referrer']);
            if($this->referralRepository->refereds($referer['uuid'])->count()>=2){
                return ["success"=>false,"message"=>"You have exceeded the limit of direct downlines registration. Please proceed with a placement.",
                "status"=>400];
            }

            $data['uuid'] = $this->generateUUID();
            $data['password'] = bcrypt($data['password']);
            $verifyCode = Str::random(10);
            $data['verification_code'] = $verifyCode;
            
            if(!$referer){
                return ["success"=>false,"message"=>"Referrer not found","status"=>400];
            }

            $placement = null;
            if(! is_null($data['placer'])){
                $placement = $this->userRepository->getUser($data['placer'])['uuid'];
                if(is_null($placement)){
                    return ["success"=>false,"message"=>"Placer not found","status"=>400];
                }
            }

            DB::transaction(function () use ($data,$referer,$placement) {
                
                $this->userRepository->create($data);
                $refData = ['referrer_uuid'=>$referer['uuid'],'referred_uuid'=>$data['uuid'],'placer_uuid'=>$placement];
                $this->referralRepository->create($refData);
            });
            
            Mail::to($data['email'])->queue((new UserConfirmationEmail($data)));

        } catch (Exception $e) {
            return $this->logger($e,"Error creating user");
        }
        return ["success"=>true,
        "message"=>"user created sucessfully","status"=>200];
    }

    public function details(string $uuid)
    {
        try {
            $data = $this->userRepository->details($uuid);
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching user details");
        }
        return ["success"=>true, "data"=>$data,
        "message"=>"user fetched sucessfully","status"=>200];
    }

    /**
     * check user's email exists for password reset
     */
    public function userExists(string $email, $model=false)
    {
       return $this->userRepository->userExists($email,$model);
    }

    public function updateVerificationCode($email,$code)
    {
        return $this->userRepository->updateVerificationCode($email,$code);
    }

    public function checkVerificationCode($email,$code)
    {
        return $this->userRepository->checkVerificationCode($email,$code);
    }

    public function updatePassword(string $email, string $password)
    {
        $password = bcrypt($password);
        return $this->userRepository->updatePassword($email,$password);
    }

    public function verifyEmail(string $email)
    {
        return $this->userRepository->verifyEmail($email);
    }

    public function resendEmailConfirmationCode($email)
    {
        $verifyCode = Str::random(5);
        $data['verification_code'] = $verifyCode;
        $this->userRepository->updateVerificationCode($email,$verifyCode);
        Mail::to($email)->queue((new UserConfirmationEmail($data)));
    }

    /**
     * admin get user details
     */
    public function userDetails(string $user_uuid)
    {
        try {
            $data = $this->userRepository->details($user_uuid);
        } catch (Exception $e) {
            return $this->logger($e,"Error admin fetching user");
        }
        return ["success"=>true, "data"=>$data,
        "message"=>"user fetched sucessfully","status"=>200];
    }

    public function delete(string $user_uuid)
    {
        try {
            $this->userRepository->delete($user_uuid);
        } catch (Exception $e) {
            return $this->logger($e,"Error deleting user");
        }
        return ["success"=>true,
        "message"=>"user deleted sucessfully","status"=>200];
    }

    public function update(string $user_uuid, array $data)
    {
        try {
           $this->userRepository->update($user_uuid,$data);
        } catch (Exception $e) {
            return $this->logger($e,"Error updating user");
        }
        return ["success"=>true,
        "message"=>"user updated sucessfully","status"=>200];
    }

    public function uplineDetails(string $uuid)
    {
        try {
           $ref = $this->referralRepository->get($uuid);
           $data = $this->userRepository->details($ref->referrer_uuid);
           $package = $this->packageRepo->get($data['package_id']);
           $data['package'] = $package->name; 
           return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching upline details");
        }
    }

    public function downlines(string $uuid)
    {
        try {
           $refs = $this->referralRepository->refereds($uuid,true);//->limit(10);
           //info('refs',[$refs]);
           $refs = $refs->filter(function($ele){
              return User::where('uuid','=',$ele->referred_uuid)->first()->packagePayment()->first() !== null;
           });

           //info('refs--',[$refs]);

            $data = $refs->map(function($ele){
                //info('dl',[\App\Models\Referral::where('referrer_uuid',$ele->referred_uuid)->get()]);//[$this->referralRepository->refereds($ele->referred_uuid)]);
              return  [
                'id'=>$ele->id,
                'name'=>User::where('uuid','=',$ele->referred_uuid)->first()->first_name.' '.User::where('uuid','=',$ele->referred_uuid)->first()->last_name,
                'username'=>User::where('uuid','=',$ele->referred_uuid)->first()->username,
                'package'=>Package::find(User::where('uuid','=',$ele->referred_uuid)->first()->package_id)->name,
                'downlines'=>$this->referralRepository->refereds($ele->referred_uuid)->count()
              ];
           }); 

           return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching downlines");
        }
    }

    public function directDownlines(string $uuid)
    {
        try {
            $refs = $this->referralRepository->refereds($uuid);//->limit(10);
            //info('refs',[$refs]);
            $refs = $refs->filter(function($ele){
               return User::where('uuid','=',$ele->referred_uuid)->first()->packagePayment()->first() !== null;
            });
 
            //info('refs--',[$refs]);
 
             $data = $refs->map(function($ele){
                 //info('dl',[\App\Models\Referral::where('referrer_uuid',$ele->referred_uuid)->get()]);//[$this->referralRepository->refereds($ele->referred_uuid)]);
               return  [
                 'id'=>$ele->id,
                 'email'=>User::where('uuid','=',$ele->referred_uuid)->first()->email,
                 'name'=>User::where('uuid','=',$ele->referred_uuid)->first()->first_name.' '.User::where('uuid','=',$ele->referred_uuid)->first()->last_name,
                 'username'=>User::where('uuid','=',$ele->referred_uuid)->first()->username,
                 'package'=>Package::find(User::where('uuid','=',$ele->referred_uuid)->first()->package_id)->name,
                 'photo_path'=>User::where('uuid','=',$ele->referred_uuid)->first()->profile()->first()->photo_path
               ];
            }); 
 
            return ['data'=>$data,'status'=>200];
         } catch (Exception $e) {
            return $this->logger($e,"Error fetching direct downlines");
         }
    }

    public function genealogy(string $uuid)
    {
        try {
           $data =  (new GenealogyService)->getDownlineTreeStructure($uuid);
           //info('gen',[$data]);
           return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching genealogy");
        }
    }

    public function inviteGuest(string $uuid,$data)
    {
        try {
            $user = $this->userRepository->getUser($uuid);
            $mailData = ['sender_name'=>$user['first_name'].''.$user['last_name'],
                    'sender_username'=>$user['username'], 'receiver_email'=>$data['email'],
                    'referrer'=>$data['referrer']
                ];
           Mail::to($data['email'])->queue(new InviteGuestMail($mailData));
           return ['status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error sending guest email");
        }
    }

    public function totalRegistrations()
    {
        try {
            $data = $this->userRepository->totalRegistrations();
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching total registrations");
        }
    }

    public function totalRegistrationPV()
    {
        try {
            $data = $this->packagePaymentRepo->totalRegistrationPV();
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching total registration pv");
        }
    }

    public function user(string $uuid)
    {
        try {
            $data = $this->userRepository->getUser($uuid);
            return ['data'=>$data,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error fetching user");
        }
    }

    public function sendMessage(string $uuid, array $data)
    {
        try {
            $user = $this->userRepository->getUser($uuid);
            Mail::to($user['email'])->send(new \App\Mail\SendMessage($data));
            return ['status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error sending message to user");
        }
    }

    public function paidUsers()
    {
        try {
            $users = $this->packagePaymentRepo->paidUsers();
            return ['data'=>$users,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error getting paid users");
        }
    }

    public function totalPaidUsers()
    {
        try {
            $users = $this->packagePaymentRepo->paidUsers(true);
            return ['data'=>$users,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error getting total paid users");
        }
    }

    public function sumPaidUsers()
    {
        try {
            $users = $this->packagePaymentRepo->sumPaidUsers();
            return ['data'=>$users,'status'=>200];
        } catch (Exception $e) {
            return $this->logger($e,"Error summing paid users");
        }
    }

    protected function generateUUID()
    {
        $uuid = Str::random(16);
        if($this->userRepository->uuidExists($uuid)){
            $this->generateUUID();
        }
        return $uuid;
    }


}

<?php
namespace App\Services;

use App\Repositories\RankRepository;
use \Exception;
use Illuminate\Support\Facades\Log;
class RankService{

    private $rankRepo;
    public function __construct(){
        $this->rankRepo = new RankRepository;
    }

    public function all()
    {
        try {
            $data = $this->rankRepo->all();
            return ['data' => $data, 'status' => 200];
        } catch (Exception $e) {
            Log::error("all ranka error", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function get(Int $id)
    {
        try {
            $data = $this->rankRepo->get($id);
            return ['data' => $data, 'message' => 'Rank fetched successfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("get rank error", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function create(array $data)
    {
        try {
           $rank = $this->rankRepo->create($data);
            return ['data' => $rank, 'message' => 'Rank created succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("create rank erro", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $this->rankRepo->update($id, $data);
            return ['message' => 'Rank updated succesfully', 'success'=>true, 'status' => 200];
        } catch (Exception $e) {
            Log::error("update rank erro", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }

    public function delete(int $id)
    {
        try {
            $this->rankRepo->table->delete($id);
            return ['message' => 'Rank deleted succesfully', 'status' => 200];
        } catch (Exception $e) {
            Log::error("delete rank erro", [$e]);
            $message = env('APP_ENV') == 'production' ? 'An error occured' : $e->getMessage();
            return ['message' => $message, 'status' => 500];
        }
    }
}
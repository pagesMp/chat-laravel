<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
         
    public function adUserGame($id){
    
        try {
         Log::info('Uniendote al game');

            $userId = auth()->user()->id;
            $gameId = $id;
 
            $user = User::query()->find($userId);         
            $game = Game::query()->find($gameId);
            
            $user->game()->attach($game);  

            return response()->json(
            [
                'success' => true,
                'message' => 'Congrats you added correctly to this game',
                'data' => $user , $game
            ], 
        200
        );
 
        } catch (\Exception $exception){
         Log::error('Error cant joing to this game' . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'You cant joing to this game',
                ], 
            400
            );
        }
     } 

     public function leaveUserGame($id){
    
        try {
         Log::info('Saliendo del game');

            $userId = auth()->user()->id;
            $gameId = $id;
 
            $user = User::query()->find($userId);         
            $game = Game::query()->find($gameId);
            $asoc_existe = DB::table('game_user')->where('game_id', $gameId)->value('id');
            Log::info('asco ' . $asoc_existe);

            if(!$asoc_existe){
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'User is not added to this game'                        
                    ], 
                400
                );
            }
            
            $user->game()->detach($game);  

            return response()->json(
            [
                'success' => true,
                'message' => 'Congrats you leave from this game',
                'data' => $user , $game
            ], 
        200
        );
 
        } catch (\Exception $exception){
         Log::error('Error cant leave from this game' . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'You cant leave from this game',
                ], 
            400
            );
        }
     }
    
    public function findParties($id){

        try {
            $game = Game::query()->find($id);
            $parties = Party::query()->where('game_id', $id)->get();

            if(count($parties) == 0 ){
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Game have no parties'                        
                    ], 
                400
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'This are the parties from this game',
                    'data' => $parties , $game
                ], 
            200
            );

        } catch (\Exception $exception){
            Log::error('Error cant find parties' . $exception->getMessage());
   
               return response()->json(
                   [
                       'success' => false,
                       'message' => 'You cant find parties',
                   ], 
               400
               );
        }
    }
     
    public function createGame(Request $request){
        try {
            
            $gameName = $request->input('name');
            $gameCategory = $request->input('category');   
            
            $game = new Game();
            $game->name = $gameName;
            $game->category = $gameCategory;
            $game->save(); 
           
            return response()->json(
                [
                    'success'=> true,
                    'message'=> 'Party successfully created',
                    'data'=> $game
                ],
            200
            );

        }catch (\Exception $exception){
            Log::error('Error cant create a game' . $exception->getMessage());
   
               return response()->json(
                   [
                       'success' => false,
                       'message' => 'You cant create a game',
                   ], 
               400
               );
           }
    }
}
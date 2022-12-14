<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartyController extends Controller
{
    public function addUserParty($id)
    {
        try {
            Log::info('entrando a la party');
            $userId = auth()->user()->id;
            $user = User::query()->find($userId);
            $user->party()->attach($id);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Congrats you added correctly to this party',
                    'data' => $user,
                    'party' => $id
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error('Error cant joing to this party' . $exception->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'You cant joing to this party',
                ],
                400
            );
        }
    }

    public function createParty(Request $request, $id)
    {
        try {
            $partyName = $request->input('name');
            $userId = auth()->user()->id;
            $gameId = $id;

            $party = new Party();
            $party->name = $partyName;
            $party->game_id = $gameId;
            $party->user_id = $userId;
            $party->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Party successfully created',
                    'data' => $party,
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error('Error cant create a party' . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'You cant create a party',
                ],
                400
            );
        }
    }

    public function leaveUserParty($id)
    {
        try {
            Log::info('Saliendo de la party');
            $userId = auth()->user()->id;
            $partyId = $id;
            $user = User::query()->find($userId);
            //recuperamos la party en la que el user esta unido
            $party = Party::query()->find($partyId);
            $isInParty = DB::table('party_user')->where('user_id', $userId)->pluck('party_id')->contains($partyId);

            //validamos que si NO esta asociado, nos salte error 
            if (!$isInParty) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'User is not added to this party'
                    ],
                    400
                );
            }

            $user->party()->detach($partyId);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Congrats you leave from this party',
                    'data' => $user,
                    'party' => $party
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error('Error cant leave from this party' . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'You cant leave from this party',
                ],
                400
            );
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Team;

class LeaderboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'ok' => true,
            'leaderboard' => Team::leaderboard(),
        ]);
    }
}

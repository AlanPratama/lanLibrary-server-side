<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rentlogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RentController extends Controller
{
    public function newRent(Request $req)
    {
        $rentLogs = Rentlogs::where('user_id', Auth::user()->id)
            ->where('status', 'Need Verification')
            ->where('status', 'Overdue')
            ->count();


        if ($rentLogs > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already rent a book',
            ]);
        } else {
            $data = [
                'code' => Str::random(4) . '-' . Auth::user()->slug . '-' . Carbon::now()->toTimeString(),
                'user_id' => Auth::user()->id,
                'book_id' => $req->book_id,
                'date_start' => Carbon::now()->toDateString(),
                'date_finish' => Carbon::now()->addDays($req->borrowDays)->toDateString(),
            ];

            $rent = Rentlogs::create($data);

            return response()->json([
                'status' => 'success',
                'data' => $rent,
            ]);
        }
    }

    public function cancelRent($code)
    {
        $rent = Rentlogs::where('code', $code)->where('status', 'Need Verification')->first();

        if ($rent) {
            $rent->status = 'Canceled';
            $rent->save();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESSFULLY CANCELED RENT LOGS',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }

    }

    public function verifyRent($code)
    {
        $rent = Rentlogs::where('code', $code)->where('status', 'Need Verification')->first();

        if ($rent) {
            $book = Book::findOrFail($rent->book_id);
            if ($book->total_book < 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'BOOK OUT OF STOCK',
                ]);

            } else {
                $book->total_loan += 1;
                $book->total_book -= 1;
                $book->save();

                $rent->status = 'Verified';
                $rent->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESSFULLY VERIFIED RENT LOGS',
                    'data' => $rent
                ]);
            }

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }

    public function returnRent($code)
    {
        $rent = Rentlogs::where('code', $code)->where('status', 'Verified')->first();

        if ($rent) {
            $book  = Book::findOrFail($rent->book_id);
            $book->total_book += 1;
            $book->save();

            $rent->status = 'Returned';
            $rent->return = Carbon::now()->toDateString();
            $rent->save();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESSFULLY RETURNED RENT LOGS',
            ]);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ])
        }
    }
}

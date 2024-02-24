<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rentlogs;
use App\Models\Reviews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RentController extends Controller
{

    public function getOneMyRent($code)
    {
        $rent = Rentlogs::with('users', 'books')
            ->where('code', $code)
            ->where('user_id', Auth::user()->id)
            ->first();

        if ($rent) {
            return response()->json([
                'status' => 'success',
                'data' => $rent,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }

    public function getAllMyRent()
    {
        $rent = Rentlogs::with('users', 'books')
            ->where('user_id', Auth::user()->id)
            ->get();

        if ($rent->count() > 0) {
            return response()->json([
                'status' => 'success',
                'data' => $rent,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }


    public function getMyNormalRent()
    {
        $rent = Rentlogs::with('users', 'books')
            ->where('status', '!=', 'Overdue')
            ->where('status', '!=', 'Broken')
            ->where('status', '!=', 'Missing')
            ->where('user_id', Auth::user()->id)
            ->get();

        if ($rent->count() > 0) {
            return response()->json([
                'status' => 'success',
                'data' => $rent,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }

    public function getMyViolationRent()
    {
        $rent = Rentlogs::with('users', 'books')
            ->where('status', 'Overdue')
            ->where('status', 'Broken')
            ->where('status', 'Missing')
            ->where('user_id', Auth::user()->id)
            ->get();

        if ($rent->count() > 0) {
            return response()->json([
                'status' => 'success',
                'data' => $rent,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }






















    public function needVerification()
    {
        $rent = Rentlogs::where('user_id', Auth::user()->id)->where('status', 'Need Verification')->first();

        if ($rent) {
            return response()->json([
                'status' => 'success',
                'data' => $rent,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }

    public function normalRent()
    {
        $rent = Rentlogs::where('status', 'Returned')->where('user_id', Auth::user()->id)->get();
    }

    public function getViolationRent()
    {
        $rent = Rentlogs::where('status', 'Overdue')
            ->where('status', 'Broken')
            ->where('status', 'Missing')
            ->where('user_id', Auth::user()->id)
            ->get();

        if ($rent->count() > 0) {
            return response()->json([
                'status' => 'success',
                'data' => $rent,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }


    // REVIEWS || REVIEWS || REVIEWS || REVIEWS || REVIEWS || REVIEWS || REVIEWS || REVIEWS

    public function rentReview(Request $req, $code)
    {
        $rent = Rentlogs::where('code', $code)->where('user_id', Auth::user()->id)->first();

        if ($rent) {
            if ($rent->reviews != null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'YOU ALREADY REVIEWED THE BOOK',
                ]);
            } else {
                $data = [
                    'code' => Str::random(14) . Auth::user()->slug,
                    'user_id' => $rent->user_id,
                    'book_id' => $rent->book_id,
                    'score' => $req->score,
                    'comment' => $req->comment,
                ];

                $review = Reviews::create($data);
                $rent->reviews = $review->id;

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESSFULLY REVIEWED THE BOOK',
                    'review' => $review,
                    'rent' => $rent,
                ]);

            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }

    }


    public function updateReview(Request $req, $code)
    {
        $review = Reviews::where('code', $code)->first();

        if ($req->score == null && $req->comment == null) {
            if ($review) {
                if ($req->score != null) {
                    $review->score = $req->score;
                }

                if ($req->comment) {
                    $review->comment = $req->comment;
                }

                $review->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESSFULLY UPDATED THE REVIEW',
                    'data' => $review
                ]);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'REVIEW NOT FOUND',
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'from' => 'noReq',
                'message' => 'PLEASE INPUT SCORE OR COMMENT BELOW',
            ]);
        }

    }


    public function deleteReview(Request $req, $code)
    {
        $review = Reviews::where('code', $code)->where('user_id', Auth::user()->id)->first();

        if ($review) {
            $review->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESSFULLY DELETED THE REVIEW',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'REVIEW NOT FOUND',
            ]);
        }
    }




















    public function newRent(Request $req)
    {
        $needVerify = Rentlogs::where('status', 'Need Verification')->count();
        $verified = Rentlogs::where('status', 'Verified')->count();

        if ($needVerify + $verified > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already rent a book',
                'rent' => $needVerify + $verified
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
                'rent' => $needVerify + $verified
             ]);
        }
    }

    public function cancelRent($code)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'officer') {
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
        } else {
            $rent = Rentlogs::where('code', $code)
                ->where('status', 'Need Verification')
                ->where('user_id', Auth::user()->id)
                ->first();

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
                    'message' => 'RENT LOGS NOT FOUND! MAYBE BECAUSE IT IS NOT YOUR RENTLOGS?',
                ]);
            }
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
        $now = Carbon::now()->toDateString();

        if ($rent) {
            if ($rent->date_finish < $now) {
                $book  = Book::findOrFail($rent->book_id);
                $book->total_book += 1;
                $book->save();

                $dayLate = Carbon::parse($rent->date_finish)->diffInDays($now);
                $rent->day_late = $dayLate;
                $rent->penalties = $rent->day_late * 5000;

                $rent->status = 'Returned Overdue';
                $rent->return = Carbon::now()->toDateString();
                $rent->save();

                return response()->json([
                    'status' => 'success',
                    'violation' => true,
                    'message' => 'MEMBER HAVE BEEN OVERDUE',
                    'dayLate' => $dayLate,
                    'penalties' => $rent->penalties,
                    'data' => $rent->with('users', 'books')->first()
                ]);

            } else {
                $book  = Book::findOrFail($rent->book_id);
                $book->total_book += 1;
                $book->save();

                $rent->status = 'Returned';
                $rent->return = Carbon::now()->toDateString();
                $rent->save();

                return response()->json([
                    'status' => 'success',
                    'violation' => false,
                    'message' => 'SUCCESSFULLY RETURNED RENT LOGS',
                    'data' => $rent->with('users', 'books')->first(),
                ]);
            }

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }
    }


    public function violationRent(Request $req, $code)
    {
        $rent = Rentlogs::where('code', $code)->where('status', 'Verified')->first();

        if ($rent) {
            if ($req->status == 'Broken') {
                $rent->status = 'Broken';
                $rent->penalties = $req->penalties;
                $rent->return = Carbon::now()->toDateString();
                $rent->save();
            } else {
                $rent->status = 'Missing';
                $rent->penalties = $req->penalties;
                $rent->return = Carbon::now()->toDateString();
                $rent->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESSFULLY VIOLATION RENT LOGS',
                'data' => $rent,
            ]);

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'RENT LOGS NOT FOUND',
            ]);
        }

    }

}

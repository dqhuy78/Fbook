<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\OwnerRepository;
use App\Repositories\Contracts\BookUserRepository;
use App\Repositories\Contracts\ReputationRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\FollowRepository;
use App\Eloquent\Owner;
use App\Eloquent\BookUser;
use Auth;

class UserController extends Controller
{
    protected $owner;

    protected $bookUser;

    protected $reputation;

    protected $user;

    protected $follow;

    public function __construct(
        OwnerRepository $owner,
        BookUserRepository $bookUser,
        ReputationRepository $reputation,
        UserRepository $user,
        FollowRepository $follow
    ) {
        $this->middleware('auth');
        $this->owner = $owner;
        $this->bookUser = $bookUser;
        $this->reputation = $reputation;
        $this->user = $user;
        $this->follow = $follow;
    }

    public function myProfile()
    {
        $user = Auth::user()
            ->load([
                'office',
                'ownerBooks',
                'followers',
                'followings',
            ]);

        return $this->getUserInfo($user);
    }

    public function getBooks($status)
    {
        $data = [
            'user_id' => Auth::id(),
            'type' => $status,
        ];
        $with = [
            'book',
        ];
        $books = $this->bookUser->getData($data, $with)->pluck('book');
        $books = $books->chunk(config('view.paginate.book_profile'));

        return view('layout.section.profile_books', compact('books', 'status'));
    }

    public function getUserInfo($user)
    {
        $books = $user->ownerBooks->chunk(config('view.paginate.book_profile'));
        $status = config('view.status.sharing');
        $followingIds = Auth::user()->followings->pluck('id')->toArray();
        $followers = $user->followers->chunk(config('view.paginate.follow_user'));
        $followings = $user->followings->chunk(config('view.paginate.follow_user'));

        return view('user.profile', compact('user', 'books', 'status', 'followings', 'followers', 'followingIds'));
    }

    public function getUser($id)
    {
        try {
            if ($id != Auth::id()) {
                $selects = [
                    'id',
                    'name',
                    'email',
                    'avatar',
                    'reputation_point',
                ];
                $with = [
                    'office',
                    'ownerBooks',
                    'followers',
                    'followings',
                ];
                $user = $this->user->find($id, $with, $selects);

                return $this->getUserInfo($user);
            } else {
                return redirect()->route('my-profile');
            }
        } catch (Exception $e) {
            return view('error');
        }
    }

    public function sharingBook($id)
    {
        try {
            $result = $this->owner->store([
                'user_id' => Auth::id(),
                'book_id' => $id,
            ]);
            $data = Auth::user()->only('id', 'name', 'avatar');
            $data['avatar'] = $data['avatar'];
            if ($result !== true) {
                $record = $this->reputation->store([
                    'point' => config('model.reputation.share_book'),
                    'user_id' => $data['id'],
                    'target_type' => config('model.target_type.book'),
                    'target_id' => $id,
                ]);
                $point = Auth::user()->reputation_point + $record->point;
                $this->user->update(
                    Auth::id(),
                    [
                        'reputation_point' => $point,
                    ]
                );
            }

            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function removeOwner($id)
    {
        $this->owner->destroy([
            'user_id' => Auth::id(),
            'book_id' => $id,
        ]);

        return Auth::id();
    }

    public function borrowingBook(Request $request, $id)
    {
        $request->merge([
            'type' => config('model.book_user.type.waiting'),
            'book_id' => $id,
            'user_id' => Auth::id(),
            'approved' => config('model.approved.default'),
        ]);

        return $this->bookUser->store($request->all());
    }

    public function cancelBorrowing($id)
    {
        return $this->bookUser->destroy([
            'book_id' => $id,
            'user_id' => Auth::id(),
        ]);
    }

    public function follow($id)
    {
        try {
            $result = $this->follow->store([
                'following_id' => Auth::id(),
                'follower_id' => $id,
            ]);

            if ($result !== true) {
                $record = $this->reputation->store([
                    'point' => config('model.reputation.follow'),
                    'user_id' => $id,
                    'target_type' => config('model.target_type.user'),
                    'target_id' => Auth::id(),
                ]);
                $point = $this->user->find($id)->reputation_point + $record->point;
                $this->user->update(
                    $id,
                    [
                        'reputation_point' => $point,
                    ]
                );
            }
        } catch (Exception $e) {

        }
    }

    public function unfollow($id)
    {
        return $this->follow->destroy([
            'following_id' => Auth::id(),
            'follower_id' => $id,
        ]);
    }

}
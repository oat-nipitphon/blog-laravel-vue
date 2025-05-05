<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\UserWallet;
use App\Models\Post;
use App\Models\PostType;
use App\Models\PostImage;
use App\Models\PostDeletetion;
use App\Models\PostPop;

class PostController extends Controller
{


    // Function format date time
    private function dateTimeFormatTimeZone()
    {
        return Carbon::now('Asia/bangkok')->format('Y-m-d H:i:s');
    }


    public function getTypePost()
    {
        $postTypes = PostType::all();
        return response()->json([
            'postTypes' => $postTypes,
        ], 200);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $posts = Post::with([
                'postType',
                'postImage',
                'postPopularity',
                'user',
                'user.userImage',
                'user.userProfile',
                'user.userProfileContact',
                'user.userProfileImage',
                'user.followersProfiles',
                'user.popularityProfiles'
            ])
                ->where('deletetion_status', 'false')
                ->where('block_status', 'false')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($post) {
                    return $post ? [

                        'id' => $post->id,
                        'title' => $post->post_title,
                        'content' => $post->post_content,
                        'userID' => $post->user_id,
                        'createdAt' => $post->created_at,
                        'updatedAt' => $post->updated_at,

                        'postType' => $post->postType ? [
                            'name' => $post->postType->post_type_name,
                        ] : null,

                        'postPopularity' => $post->postPopularity ?
                        $post->postPopularity->map(function ($postPop) {
                            return [
                                'id' => $postPop->id,
                                'postID' => $postPop->post_id,
                                'userID' => $postPop->user_id,
                                'status' => $postPop->pop_status,
                            ];
                        }) : [],

                        'postImage' => $post->postImage ?
                        $post->postImage->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'imageName' => $image->image_name,
                                'imageData' => $image->image_data,
                            ];
                        }) : [],

                        'user' => $post->user ? [
                            'id' => $post->user->id,
                            'name' => $post->user->name,
                            'email' => $post->user->email,
                            'username' => $post->user->username,
                            'statusID' => $post->user->status_id
                        ] : [],

                        'userProfile' => $post->user->userProfile ? [
                            'id' => $post->user->userProfile->id,
                            'userID' => $post->user->userProfile->user_id,
                            'titleName' => $post->user->userProfile->title_name,
                            'fullName' => $post->user->userProfile->full_name,
                            'nickName' => $post->user->userProfile->nick_name,
                            'telPhone' => $post->user->userProfile->tel_phone,
                            'birthDay' => $post->user->userProfile->birth_day,
                            'createdAt' => $post->user->userProfile->created_at,
                            'updatedAt' => $post->user->userProfile->updated_at,
                        ] : [],

                        'userImage' => $post->user->userImage ?
                        $post->user->userImage->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'imageData' => $image->image_data ? $image->image_data : 'null'
                            ];
                        }) : [],

                        'userProfileContact' => $post->user->userProfileContact ?
                        $post->user->userProfileContact->map(function ($contact) {
                            return [
                                'id' => $contact->id,
                                'name' => $contact->contact_name,
                                'iconName' => $contact->contact_icon_name,
                                'iconUrl' => $contact->contact_icon_url,
                                'iconData' => $contact->contact_icon_data ? 'data:image/png;base64,'
                                    . base64_encode($contact->contact_icon_data) : null,
                            ];
                        }) : [],

                        'userFollowersProfile' => $post->user->followersProfiles ?
                        $post->user->followersProfiles->map(function ($row) {
                            return [
                                'id' => $row->id,
                                'profile_user_id' => $row->profile_user_id,
                                'followers_user_id' => $row->followers_user_id,
                                'status_followers' => $row->status_followers,
                                'created_at' => $row->created_at,
                                'updated_at' => $row->updated_at
                            ];
                        }) : [],

                        'userPopularityProfiles' => $post->user->popularityProfiles ?
                        $post->user->popularityProfiles->map(function ($row) {
                            return [
                                'id' => $row->id,
                                'user_id' => $row->user_id,
                                'user_id_pop' => $row->user_id_pop,
                                'status_pop' => $row->status_pop,
                                'created_at' => $row->created_at,
                                'updated_at' => $row->updated_at,
                            ];
                        }) : [],

                    ] : null;
                });

            if ($posts) {
                return response()->json([
                    'message' => "Laravel get posts response success.",
                    'posts' => $posts,
                ], 200);
            }

            return response()->json([
                'message' => "Laravel get posts response false",
                'posts' => $posts,
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function get posts error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function get posts error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Function Create Post
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'nullable|string',
                'typeID' => 'nullable|integer',
                'newType' => 'nullable|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $type_id = $request->typeID;
            if (!empty($request->newType)) {
                $postType = PostType::create([
                    'post_type_name' => $request->newType,
                    'created_at' => now()
                ]);
                $type_id = $postType->id;
            }

            if ($type_id) {
                $post = Post::create([
                    'post_title' => $validated['title'],
                    'post_content' => $validated['content'],
                    'refer' => $validated['refer'],
                    'type_id' => $type_id,
                    'user_id' => $validated['userID'],
                    'deletetion_status' => "false", // status 0 == false // status 1 == true
                    'block_status' => "false",
                    'created_at' => now(),
                    'created_at' => now(),
                ]);
            }

            $postImage = null;

            if ($request->file('imageFile')) {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);

                $postImage = PostImage::create([
                    'post_id' => $post->id,
                    'image_data' => $imageDataBase64,
                    'created_at' => now(),
                ]);
            }

            if ($post) {
                // เก็บ point ให้แน่ใจว่าไม่ขึ้นกับ postImage
                $user_wallet = UserWallet::firstOrCreate(
                    ['user_id' => $validated['userID']],
                    ['point' => 0]
                );
                $user_wallet->increment('point', 100);

                return response()->json([
                    'message' => 'Laravel function store successfully.',
                    'post' => $post,
                    'imageDataBase64' => $imageDataBase64 ?? null,
                ], 201);
            }

            return response()->json([
                'message' => "laravel api store response false"
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function store error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function store error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $post = Post::with(
                'postType',
                'postImage'
            )->findOrFail($id);

            $posts = [
                'id' => $post->id,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'refer' => $post->refer,
                'userID' => $post->user_id,
                'deletetionStatus' => $post->deletetion_status,
                'blockStatus' => $post->block_status,
                'createdAt' => $post->created_at,
                'updatedAt' => $post->updated_at,
                'postType' => $post->postType ? [
                    'id' => $post->postType->id,
                    'typeName' => $post->postType->post_type_name,
                ] : null,
                'postImage' => $post->postImage->isNotEmpty() ? $post->postImage->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'imagePath' => $image->image_path,
                        'imageName' => $image->image_name,
                        'imageData' => $image->image_data,
                    ];
                }) : null,
            ];


            if ($posts) {
                return response()->json([
                    'message' => "Laravel function show successfully.",
                    'posts' => $posts
                ], 200);
            }

            return response()->json([
                'message' => "Laravel function show response false !!",
                'posts' => $posts
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function show error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function show error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * Function Update Post
     */
    public function update(Request $request)
    {
        try {

            $validated = $request->validate([
                'userID' => 'required|integer',
                'postID' => 'required|integer',
                'title' => 'required|string',
                'content' => 'required|string',
                'refer' => 'nullable|string',
                'typeID' => 'nullable|integer',
                'newType' => 'nullable|string',
                'imageFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $type_id = $request->typeID;
            if (!empty($request->newType)) {
                $postType = PostType::create([
                    'post_type_name' => $request->newType,
                    'created_at' => now()
                ]);
                $type_id = $postType->id;
            }

            $post = Post::findOrFail($request->postID);

            if (!$post) {
                return response()->json([
                    'message' => "laravel check image file req false"
                ], 204);
            }

            $post->update([
                'post_title' => $validated['title'],
                'post_content' => $validated['content'],
                'refer' => $validated['refer'],
                'type_id' => $type_id,
                'deletetion_status' => "false", // status 0 == false // status 1 == true
                'block_status' => "false",
                'updated_at' => now(),
            ]);

            if ($request->hasFile('imageFile')) {
                $imageFile = $request->file('imageFile');
                $imageData = file_get_contents($imageFile->getRealPath());
                $imageDataBase64 = base64_encode($imageData);
            }

            $postImage = PostImage::where('post_id', $post->id)->first();

            if ($postImage) {
                $postImage->update([
                    'post_id' => $post->id,
                    'image_data' => $imageDataBase64,
                    'updated_at' => now(),
                ]);
            } else {
                PostImage::create([
                    'post_id' => $post->id,
                    'image_data' => $imageDataBase64,
                    'created_at' => now(),
                ]);
            }

            return response()->json([
                'message' => 'Laravel function store successfully.',
                'post' => $post,
                'imageDataBase64' => $imageDataBase64,
            ], 201);
        } catch (\Exception $error) {

            Log::error("Laravel function store error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function store error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            DB::beginTransaction(); // ใช้ Transaction เพื่อความปลอดภัย

            $post = Post::findOrFail($id);

            if ($post) {
                PostImage::where('post_id', $id)->delete();
                PostPop::where('post_id', $id)->delete();
                PostDeletetion::where('post_id', $id)->delete();
                $post->delete();
            }

            DB::commit(); // บันทึกการเปลี่ยนแปลง

            return response()->json([
                'message' => "Laravel API delete success",
                'postID' => $post->id,
            ], 200);
        } catch (\Exception $error) {

            Log::error("Laravel function destroy error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function destroy error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function recoverSelected(Request $request)
    {
        try {

            $ids = $request->ids;

            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'message' => 'laravelapi recover ids false'
                ], 400);
            }


            $recoverPosts = Post::whereIn('id', $ids)->get();

            foreach ($recoverPosts as $post) {
                $post->update([
                    'deletetion_status' => 'false',
                    'updated_at' => now()
                ]);
            }


            return response()->json([
                'message' => 'laravelapi updated recover success',
                'recoverPosts' => $recoverPosts
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'laravel api function recover selected error',
                'error' => $error->getMessage()
            ]);
        }
    }

    public function deleteSelected(Request $request)
    {
        try {

            DB::beginTransaction();

            Post::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'message' => "Posts deleted successfully",
                'request' => $request
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack(); // Rollback ถ้ามีข้อผิดพลาดเกิดขึ้น
            return response()->json([
                'message' => "Error in delete function: " . $error->getMessage()
            ], 500); // Return status 500 for internal server error
        }
    }

    public function postStore(string $postID)
    {
        try {

            $post = Post::findOrFail($postID);

            $dateTime = Carbon::now('Asia/Bangkok')->format('Y-m-d H:i:s');

            if (!$post) {
                return response()->json([
                    'message' => "Post function destroy response false !!",
                    'post' => 'id' . $postID,
                ], 404);
            }

            $post->update([
                'deletetion_status' => 'true',
            ]);

            $postDeletetion = PostDeletetion::create(
                [
                    'post_id' => $post->id,
                    'date_time_delete' => $dateTime,
                    'deletetion_status' => 'true',
                ]
            );

            if (!$postDeletetion) {
                return response()->json([
                    'message' => "Post function destroy response false !!",
                    'post' => $post,
                    'postDeletetion' => 'id' . $postDeletetion,
                ], 404);
            }

            return response()->json([
                'message' => "Post function destroy response false !!",
                'post' => $post
            ], 201);


        } catch (\Exception $error) {
            return response()->json([
                'message' => "Laravel function postStore error " . $error->getMessage()
            ], 500);
        }
    }

    /**
     * Recover get post data.
     */
    public function recoverGetPost(string $userID)
    {
        try {

            $recoverPosts = Post::with('postType')
                ->where('user_id', $userID)
                ->where('deletetion_status', 'true')
                ->where('block_status', 'false')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($recoverPosts) {

                return response()->json([
                    'message' => "Laravel function recoverGetPosts successfully.",
                    'recoverPosts' => $recoverPosts
                ], 200);
            } else {

                return response()->json([
                    'message' => "Laravel function recoverGetPosts response false !!",
                    'userID' => $userID,
                    'recoverPosts' => $recoverPosts
                ], 204);
            }
        } catch (\Exception $error) {

            Log::error("Laravel function recover post error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function recover get post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Recover update status post.
     */
    public function recoverPost(string $postID)
    {
        try {

            $statusRecoverPost = Post::where('id', $postID)->first();

            if ($statusRecoverPost) {
                $statusRecoverPost->update([
                    'deletetion_status' => 'false'
                ]);
            }

            if ($statusRecoverPost) {
                return response()->json([
                    'message' => "laravel recoverPost successfullry.",
                    'post' => $statusRecoverPost
                ], 201);
            }

            return response()->json([
                'message' => "laravel recoverPost response false !!",
                'postID' => $postID,
                'post' => $statusRecoverPost
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function recover post error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function recover post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Pop like post
     */
    public function postPopLike(string $userID, string $postID, string $popStatusLike)
    {
        try {
            // Check if the user already reacted
            $existingReaction = PostPop::where('user_id', $userID)
                ->where('post_id', $postID)
                ->first();

            // dd($existingReaction);

            if ($existingReaction) {
                // Toggle reaction
                if ($existingReaction->pop_status === $popStatusLike) {
                    $existingReaction->delete();
                } else {
                    $existingReaction->update(['pop_status' => $popStatusLike]);
                }
            } else {
                // Create new reaction
                PostPop::create([
                    'post_id' => $postID,
                    'user_id' => $userID,
                    'pop_status' => $popStatusLike,
                ]);
            }

            $updatedReactions = PostPop::where('post_id', $postID)->get();

            if ($updatedReactions) {



                return response()->json([
                    'message' => "Laravel function postPopLike successfully.",
                    'updatedReactions' => $updatedReactions
                ], 200);
            }

            return response()->json([
                'message' => "Laravel function postPopLike response false !!",
                'updatedReactions' => $updatedReactions
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function postPopLike post error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function like post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Pop dis loke post
     */
    public function postPopDisLike(string $userID, string $postID, string $popStatusDisLike)
    {
        try {
            $existingReaction = PostPop::where('user_id', $userID)
                ->where('post_id', $postID)
                ->first();

            if ($existingReaction) {
                if ($existingReaction->pop_status === $popStatusDisLike) {
                    $existingReaction->delete();
                } else {
                    $existingReaction->update(['pop_status' => $popStatusDisLike]);
                }
            } else {

                PostPop::create([
                    'post_id' => $postID,
                    'user_id' => $userID,
                    'pop_status' => $popStatusDisLike,
                ]);
            }

            $updatedReactions = PostPop::where('post_id', $postID)->get();

            if ($updatedReactions) {
                return response()->json([
                    'message' => "Laravel function postPopDisLike successfully.",
                    'updatedReactions' => $updatedReactions
                ], 200);
            }

            return response()->json([
                'message' => "Laravel function postPopDisLike response false !!",
                'updatedReactions' => $updatedReactions
            ], 204);
        } catch (\Exception $error) {

            Log::error("Laravel function postPopDisLike error", [
                'error' => $error->getMessage(),
                'trace' => $error->getTraceAsString()
            ]);

            return response()->json([
                'message' => "Laravel function dis like post error",
                'error' => $error->getMessage()
            ], 500);
        }
    }


    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}

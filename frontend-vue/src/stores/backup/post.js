import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'

export const usePostStore = defineStore('postStore', {
    state: () => ({
        storePost: null,
        storePosts: null,
        errors: {}
    }),
    actions: {

        async apiGetPostTypes() {
            try {
                const res = await fetch(`/api/postTypes`, {
                    method: "GET"
                })

                const data = await res.json()

                if (res.ok) {
                    return data.postTypes;
                } else {
                    console.log("data post type false", data.error)
                }

            } catch (error) {
                console.error("store function api get post type error", error)
            }
        },

        async apiGetPosts() {
            try {
                const res = await fetch(`/api/posts`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "multipart/form-data",
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    return data.posts;
                } else {
                    console.log("store api get posts false", data.error);
                }

            } catch (error) {
                console.error("store function api get posts error", error)
            }
        },

        async apiCreatePostNew(formData) {
            try {
                const res = await fetch(`/api/posts`, {
                    method: "POST",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(formData)
                });
                const data = await res.json();
                if (data.ok) {
                    this.storePost = data.createPostNew;
                    const router = useRouter();
                    router.push({ name: 'DashboardView' });
                } else {
                    console.log("store function api create post new success", data.error);
                }
            } catch (error) {
                console.error("store function api create post new error", error)
            }
        },

        async apiGetPost(post) {
            try {
                const res = await fetch(`/api/posts/${post}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "multipart/form-data",
                        authorization: `Bearer ${localStorage.getItem('token')}`,
                    }
                });

                const data = await res.json();
                console.log("pinia store api get post ", data.posts);
                if (res.ok) {
                    return data.posts;
                    // this.storePost = data.posts;
                } else {
                    console.log("store get post response false :", data.error);
                }

            } catch (error) {
                console.error("store get post error :", error);
            }
        },

        async apiEditPost(formData) {
            try {
                const result = await Swal.fire({
                    title: "Confirm Edit!",
                    text: "Are you sure you want to edit this post?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm",
                });

                if (result.isConfirmed) {

                    const res = await fetch(`/api/posts/update`, formData, {
                        method: "POST",
                        headers: {
                            "Content-Type": "multipart/form-data",
                            authorization: `Bearer ${localStorage.getItem('token')}`,
                        },
                    });

                    if (res.ok) {
                        console.log("store res.ok edit post success");
                        Swal.fire({
                            title: "Success",
                            text: "Your update post successfully.",
                            icon: "success",
                            timer: 1500,
                        }).then(() => {
                            console.log("router view");
                            const router = useRouter();
                            router.push({
                                name: 'DashboardView'
                            });
                        });
                    } else {
                        console.log("store edit post false");
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    console.log("store edit post cancel ", formData);
                }

                console.log("store edit post swal false", formData);

            } catch (error) {
                console.error("store api edit post error :", error);
            }
        },

        async apiDeletePost(id) {
            try {

                const result = await Swal.fire({
                    title: "Confirm Delete!",
                    text: "Are you sure you want to delete this post?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm delete",
                    cancelButtonText: "Cancel",
                });

                if (result.isConfirmed) {

                    const res = await fetch(`/api/posts/${id}`, {
                        method: "DELETE",
                        headers: {
                            authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    });

                    const data = await res.json();

                    if (res.ok) {
                        Swal.fire({
                            title: "Confirm",
                            text: "delete post successfully.",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1200,
                            timerProgressBar: 1200,
                        }).then(() => {
                            console.log('store delete', res);
                        });
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.close()
                } else {
                    console.log("store delete post res data false");
                }

            } catch (error) {
                console.error("store apiDeletePost error", error);
            }
        },

        async apiRecoverPostDelete(id) {
            try {
                const response = await fetch(`/api/posts/delectSelected`, {
                    method: "DELETE",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                console.log("response store", response);
                const data = await response.json();
                return data;

            } catch (error) {
                console.error("store apiDeletePost error", error);
            }
        },

        async apiStorePost(postID) {
            try {
                const result = await Swal.fire({
                    title: "ยืนยัน",
                    text: "คุณต้องการจัดเก็บบทความนี้ใช่หรือไม่ ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "จัดเก็บ",
                    cancelButtonText: "ยกเลิก",
                });
        
                if (result.isConfirmed) {
                    const res = await fetch(`/api/posts/store/${postID}`, {
                        method: "POST",
                        headers: {
                            authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    });
        
                    const data = await res.json();
        
                    if (res.ok) {
                        Swal.fire({
                            title: "สำเร็จ",
                            text: "ดำเนินจัดเก็บบทความสำเร็จ.",
                            icon: "success",
                            timer: 1200,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "เกิดข้อผิดพลาด",
                            text: data?.error || "ไม่สามารถจัดเก็บบทความได้",
                            icon: "error"
                        });
                    }
        
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.close();
                    return; // ป้องกันไม่ให้โค้ดข้างล่างรันต่อ
                }
        
            } catch (error) {
                console.error("store function api store confirm", error);
                Swal.fire({
                    title: "ข้อผิดพลาด",
                    text: "ไม่สามารถดำเนินการได้ กรุณาลองใหม่อีกครั้ง",
                    icon: "error"
                });
            }
        },
        

        async apiRecoverGetPost(userID,) {
            try {
                const response = await fetch(`/api/posts/report_recover/${userID}`, {
                    method: "POST",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });

                const data = await response.json();

                if (data.error) {
                    console.log("store recover post data error", data.error);
                }

                return data.recoverPosts;

            } catch (error) {
                console.error("store recover get post error", error);
            }
        },

        async apiRecoverPost(postID) {
            try {

                if (postID) {
                    const result = await Swal.fire({
                        title: "ยืนยัน",
                        text: "คุณต้องการกู้คืนบทความนี้ใช่หรือไม่ ?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "กู้คืน",
                        cancelButtonText: "ยกเลิก"
                    });

                    if (result.dismiss === Swal.DismissReason.cancel) {

                        console.log("store apiRecoverPost Cancel recover post");

                    } else if (result.isConfirmed) {

                        const response = await fetch(`/api/posts/recover/${postID}`, {
                            method: "POST",
                            headers: {
                                authorization: `Bearer ${localStorage.getItem('token')}`
                            },
                        });

                        if (response.ok) {
                            Swal.fire({
                                title: "สำเร็จ",
                                text: "ดำเนินการกู้คืนบทความสำเร็จ",
                                icon: "success",
                                timer: 1200,
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    } else {
                        console.log("store recover post response false ", response.error);
                    }

                }

            } catch (error) {
                console.error("store recover get post error", error);
            }
        },

        async apiPostPopLike(userID, postID, popStatusLike) {
            try {
                const response = await fetch(`/api/posts/popularity/${userID}/${postID}/${popStatusLike}`, {
                    method: "POST",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });
                const data = await response.json();
                if (response.ok) {
                    console.log("Like updated successfully");
                } else {
                    console.error("Error updating like", data.error);
                }
            } catch (error) {
                console.error("API Like Error:", error);
            }
        },

        async apiPostPopDisLike(userID, postID, popStatusDisLike) {
            try {
                const response = await fetch(`/api/posts/popularity/${userID}/${postID}/${popStatusDisLike}`, {
                    method: "POST",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });
                const data = await response.json();
                if (response.ok) {
                    console.log("Dislike updated successfully.");
                } else {
                    console.error("Error updating dislike", data.error);
                }
            } catch (error) {
                console.error("API Dislike Error:", error);
            }
        },
    }
})
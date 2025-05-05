import { defineStore } from "pinia";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

export const useAdminPostStore = defineStore("adminPostStore", {
    state: () => ({
        posts: null,
        errors: {}
    }),
    actions: {

        async adminAPIGETposts() {

            try {

                const res = await fetch(`/api/admin/posts/manager`, {
                    method: "GET",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    return data.posts;
                } else {
                    console.log("admin api get post data false", data.error);
                }

            } catch (error) {
                console.error("admin api get post error", error);
            }

        },

        async adminAPIPostDelete(postID) {
            try {

                const result = await Swal.fire({
                    title: "Delete Post ?",
                    text: "Are you sure you want to delete this post?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm.",
                    cancelButtonText: "Cancel.",
                });

                if (result.isConfirmed) {

                    const res = await fetch(`/api/admin/posts/manager/${postID}`, {
                        method: "DELETE",
                        headers: {
                            authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    });

                    if (res.ok) {
                        Swal.fire({
                            title: "Success",
                            text: "Post deleted successfully.",
                            icon: "success"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Failed to delete post. Please try again.",
                            icon: "error"
                        });
                    }

                }

            } catch (error) {
                console.error("Admin delete post function error:", error);
                Swal.fire({
                    title: "Error",
                    text: "Something went wrong. Please try again later.",
                    icon: "error"
                });
            }
        },

        async adminAPIPostBlockOrUnBlock(postID, blockStatus) {
            try {
                let actionText = null;

                // Set actionText based on blockStatus
                if (blockStatus === "Block") {
                    actionText = "Block";
                }
                if (blockStatus === "Unblock") {
                    actionText = "Unblock";
                }

                // Ensure actionText is set correctly
                if (!actionText) {
                    console.error("Invalid block status provided");
                    return; // Prevent further action if blockStatus is invalid
                }

                // Confirm with SweetAlert before making the change
                const result = await Swal.fire({
                    title: `${actionText} Post?`,
                    text: `Are you sure you want to ${actionText.toLowerCase()} this post?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                });

                if (result.isConfirmed) {
                    // Make API request to block/unblock the post
                    const res = await fetch(`/api/admin/posts/blockOrUnBlock/${postID}/${blockStatus}`, {
                        method: "POST",
                        headers: {
                            authorization: `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json',
                        }
                    });

                    if (res.ok) {
                        Swal.fire({
                            title: "Success",
                            text: `Post ${actionText.toLowerCase()}ed successfully.`,
                            icon: "success"
                        }).then(() => {
                            window.location.reload(); // Reload page to reflect changes
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: `Failed to ${actionText.toLowerCase()} post. Please try again.`,
                            icon: "error"
                        });
                    }
                }
            } catch (error) {
                console.error("Admin block/unblock post function error:", error);
                Swal.fire({
                    title: "Error",
                    text: "Something went wrong. Please try again later.",
                    icon: "error"
                });
            }
        },

    },
});
import { defineStore } from "pinia";
import { useRoute } from "vue-router";
import Swal from "sweetalert2";

export const useAdminUserProfileStore = 
defineStore("adminUserProfileStore", {
    state: () => ({
        storeUserProfiles: null,
        errors: {}
    }),
    actions: {

        async adminAPIGETuserProfiles() {
            try {
                const res = await fetch(`/api/admin/userProfiles/manager`, {
                    method: "GET",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });

                const data = await res.json();
                if (res.ok) {
                     return data.userProfiles;
                } else {
                    console.log("store get user profile false :: ", data.error);
                }

            } catch (error) {
                console.error("store admin user profile error :: ", error);
            }
        },

        async adminGetDataEditUserProfile(userID) {
            try {

                const res = await fetch(`/api/admin/userProfile/manager/${userID}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "muitlpart/form-data",
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                if (res.status === 200) {
                    const data = await res.json();
                    console.log("get data userprofile success.", data.userProfile);
                    this.storeUserProfiles = data.userProfile;
                }


            } catch (error) {
                console.error("get data edit function error", error);
            }
        },
        async adminUpdateUserProfile() {
            const result = await Swal.fire({
                title: "Confirm Update!",
                text: "Are you sure you want to update this userprofile?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
            });
        },

        async adminDeleteUserProfile(userID) {
            if (userID) {
                const result = await Swal.fire({
                    title: "Confirm Delete!",
                    text: "Are you sure you want to delete this userprofile?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                });
    
                if (result.isConfirmed) {
                    try {
    
                        const res = await fetch(`/api/admin/userProfile/manager/${userID}`, {
                            method: "DELETE",
                            headers: {
                                authorization: `Bearer ${localStorage.getItem('token')}`
                            }
                        });
    
                        if (res.ok) {
                            console.log("delete userprofile success.");
                            // window.location.reload();
                        }
    
                        console.log("res status != 200 ", res);
    
                    } catch (error) {
                        Swal.fire({
                            text: error
                        });
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.close();
                }
            } else {
                return this.storeUserProfiles = userProfileID;
            }
        },

    },
});
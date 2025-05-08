import { defineStore } from 'pinia'
import Swal from 'sweetalert2'
export const useStoreUserProfile = defineStore('storeUserProfile', {
    state: () => ({
        userProfile: null,
        storeContactProfiles: null,
        errors: {},
    }),
    actions: {

        // get status user
        async apiGetStatusUser() {
            try {
                const res = await fetch(`/api/status_user`, {
                    method: "GET",
                });
                const data = await res.json();
                if (res.ok) {
                    return data.userStatus;
                }



            } catch (error) {
                console.error("function status user error", error);
            }
        },

        // get user profile all
        async apiGetAllUserProfile(userProfile) {
            try {
                const res = await fetch(`/api/user_profiles/${userProfile}`, {
                    method: "GET",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                })
                const data = await res.json();
                if (res.ok) {
                    return data.userProfiles;
                } else {
                    console.log("store user profile res false.", res);
                }
            } catch (error) {
                console.error("store user profile error api get all :: ", error);
            }
        },

        async apiGetDashboardProfile(userProfile) {
            try {
                const res = await fetch(`/api/user_profiles/${userProfile}`, {
                    method: "GET",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                })
                const data = await res.json();
                if (res.ok) {
                    return data.userProfile;
                } else {
                    console.log("store user profile res false.", res);
                }
            } catch (error) {
                console.error("store user profile error api get all :: ", error);
            }
        },

        // get user profile where profile id
        async apiGETUserProfile(userProfile) {
            try {
                const res = await fetch(`/api/user_profiles/${userProfile}`, {
                    method: "GET",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });

                if (res.ok) {
                    const data = await res.json();
                    return data.userProfile;
                } else {
                    console.log("store get user profile repsonse error");
                }

            } catch (error) {
                console.error("store api get user profile error ", error);
            }
        },

        // Update user
        async apiUpdateUser(form) {
            try {

                const result = await Swal.fire({
                    title: "Confirm Update",
                    text: "Are you sure you want to update ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "yes",
                    cancelButtonText: "no",
                });

                if (!result.isConfirmed) {
                    result.close()
                }

                const res = await fetch('/api/update/user', {
                    method: "POST",
                    headers: {
                        "Content-Type": "multipart/form-data",
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(form),
                });

                const data = await res.json();
                console.log("store update user", data.user);
                if (res.ok) {
                    Swal.fire({
                        title: "Success",
                        text: "update profile successfully",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1200,
                        timerProgressBar: 1200,
                    }).then(() => {
                        window.location.reload();
                    })
                }

            } catch (error) {
                console.error("store update user function error", error);
            }
        },

        // Update Profile
        async apiUpdateProfile(form) {
            try {
                const result = await Swal.fire({
                    title: "Confirm Update",
                    text: "Are you sure you want to update ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "yes",
                    cancelButtonText: "no",
                });
    
                if (!result.isConfirmed) {
                    result.close()
                }
    
                const res = await fetch(`/api/update/profile`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "multipart/form-data",
                        authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                    body: JSON.stringify(form)
                })

                if (res.ok) {
                    Swal.fire({
                        title: "Success",
                        text: "update profile successfully.",
                        icon: "success",
                        timer: 1200,
                        timerProgressBar: true,
                    }).then(() => {
                        window.location.reload();
                    })
                }
            } catch (error) {
                console.error("store function update profile error", error)
            }
        },

        // Upload image user profile
        async apiUploadImageUserProfile(formData) {
            try {

                const res = await fetch(`/api/user_profile/upload_image`, {
                    method: "POST",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`,
                        "Content-Type": "multipart/form-data"
                    },
                    body: formData
                });

                if (res.ok) {

                    Swal.fire({
                        title: "Upload Image success.",
                        icon: "success",
                        timer: 1200,
                        timerProgressBar: true,
                    }).then(() => {
                        Swal.close();
                        window.location.reload();
                        return res.userProfileImage;
                    });

                } else {
                    console.log("res error", res.error);
                }


            } catch (error) {
                console.error("store user profile upload image profile error :: ");
            }
        },

        // Get Contact Profile
        async apiGetContactProfile (profileID) {
            try {
                const response = await fetch(`api/profile/contacts/${profileID}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "munltipart/form-data",
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                });
                const data = await response.json();
                if (!response.ok) {
                    console.log("store get contact profile false", response);
                }

                console.log("store get contact profile success", data.contactProfiles);
                return this.storeContactProfiles = data.contactProfiles;

            } catch (error) {
                console.error("store api get contact profile", error);
            }
        },

    }
})
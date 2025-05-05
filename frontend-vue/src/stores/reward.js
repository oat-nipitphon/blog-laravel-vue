import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import axiosAPI from '@/services/axiosAPI';

export const useRewardStore = defineStore('rewardStore', {
    state: () => ({
        storeRewards: null,
        errors: []
    }),
    actions: {

        async getRewards() {
            try {
                const res = await fetch(`/api/reward/getRewards`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'muitipart/form-data',
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    this.storeRewards = data.rewards;
                } else {
                    console.log("store get reward res false", res);
                }

            } catch (error) {
                console.error('store get reward error function', error);
            }
        },

        // async getShowReward(id) {
        //     try {
        //         const res = await fetch(`/api/reward/show/${id}`, {
        //             method: 'GET',
        //             headers: {
        //                 'Content-Type': 'muitipart/form-data',
        //                 authorization: `Bearer ${localStorage.getItem('token')}`
        //             }
        //         });

        //         const data = await res.json();
        //         if (res.ok) {
        //             this.rewards = data.rewards;
        //             console.log("store get reward res true", this.rewards);
        //         } else {
        //             console.log("store get reward res false", res);
        //         }

        //     } catch (error) {
        //         console.error('store get reward error function', error);
        //     }
        // },

        async newReward(form) {
            try {
                let formData = new FormData();
                formData.append("name", form.name);
                formData.append("point", form.point);
                formData.append("quantity", form.quantity);
                formData.append("type", form.type);
                formData.append("status", form.status);
                // if (form.imageFile) {
                //     formData.append("imageFile", form.imageFile);
                // }

                const res = await fetch(`/api/reward/newRewards`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'muitipart/form-data',
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(form)
                });

                const data = await res.json();

                if (res.ok) {
                    console.log("store new reward res ok", data);
                    window.location.reload();
                } else {
                    console.log("store new reward res false", data);
                }

            } catch (error) {
                console.error('store new reward error function', error);
            }
        },

        async editReward() {
            try {

            } catch (error) {
                console.error('store edit reward error function', error);
            }
        },
        async deleteReward(id) {
            try {

                const res = await fetch(`/api/reward/delete/${id}`, {
                    method: "DELETE",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                })

                if (res.ok) {
                    console.log('store delete reward success', res);
                    // return data.recoverPosts.filter(post => post.id !== postID);
                    const data = await res.json();
                    return data.modelReward.filter(reward => reward.id !== id);
                } else {
                    console.log("store delete reward false", res);
                }


            } catch (error) {
                console.error('store delete reward error function', error);
            }
        },


        async getReportRewards(userID) {
            try {

                const res = await fetch(`/api/cartItems/getReportReward/${userID}`, {
                    method: 'GET',
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                })

                const data = await res.json();

                if (!res.ok) {
                    console.log('store get report reward request false', res);
                } else {
                    return data.userPointCounter
                }

            } catch (error) {
                console.error('store get report reward function error', error)
            }
        },


        async cancelReward(itemID) {
            try {

                const res = await axiosAPI.post(`/api/cartItems/cancel_reward/${itemID}`, {
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                console.log('cancel api reward', res);

            } catch (error) {
                console.error(error);
            }
        }


    }
})
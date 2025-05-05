import { defineStore } from "pinia";
import { useRoute } from "vue-router";
import Swal from "sweetalert2";

export const useAdminRewardStore = defineStore('adminRewardStore', {
    state: () => ({
        storeRewards: null,
        errors: {}
    }),
    actions: {  

        async storeAdminAPIGetRewards () {
            try {
                const res = await fetch('/api/admin/rewards/manager', {
                    method: "GET",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
        
                const data = await res.json();
        
                if (res.ok) {
                   return data.rewards;
                } else {
                    console.log('storeAdminAPIGetRewards api get data false', data);
                }
            } catch (error) {
                console.error('storeAdminAPIGetRewards function error', error);
            }
        }, 

        async storeAdminAPIGetReward () {
            try {
                const res = await fetch(`/api/admin/rewards/manager/${rewardID}`, {
                    method: "POST",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
        
                const data = await res.json();
        
                if (res.ok) {
                    this.storeRewards = data.rewards;
                } else {
                    console.log('storeAdminAPIGetRewards api get data false', data);
                }
            } catch (error) {
                console.error('storeAdminAPIGetRewards function error', error);
            }
        }, 

        async storeAdminUpdateReward (formData, rewardID) {
            try {
                const res = await fetch(`/api/admin/rewards/manager/${rewardID}`, {
                    method: "PUT",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    },
                    body: formData
                });

                const data = await res.json()

                if (!res.ok) {
                    console.error('store admin update reward false');
                }

                console.log('store admin update reward success', data.reward)
        

            } catch (error) {
                console.error('storeAdminUpdateReward function error', error);
            }
        },

        async storeAdminDeleteReward (rewardID) {
            try {
                const res = await fetch(`/api/admin/rewards/manager/${rewardID}`, {
                    method: "DELETE",
                    headers: {
                        authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
        
            } catch (error) {
                console.error('storeAdminDeleteReward function error', error);
            }
        },

    }
})
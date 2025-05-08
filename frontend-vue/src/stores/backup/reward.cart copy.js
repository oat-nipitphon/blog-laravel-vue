import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axiosAPI from '@/services/axiosAPI'

export const useRewardCartStore = defineStore('rewardCart', () => {
    
    const carts = ref([])
    const errors = ref({})
    const counterItems = ref([])
    const items = ref([])

    const addToCart = (reward) => {
        const existingItem = carts.value.find((item) => item.id === reward.id)
    
        if (!existingItem) {
            // เพิ่มรายการใหม่เข้า carts
            carts.value.push({ ...reward, amount: 1 })
    
            // เพิ่มเข้า counterItems
            counterItems.value.push({
                rewardID: reward.id,  
                rewardName: reward.name,
                rewardPoint: reward.point,
                rewardAmount: 1
            })
        } else {
            // ถ้ามีอยู่แล้ว เพิ่มจำนวนใน carts
            existingItem.amount += 1
    
            // อัปเดตจำนวนใน counterItems
            const counterItem = counterItems.value.find(item => item.rewardID === reward.id)
            if (counterItem) {
                counterItem.rewardAmount += 1
            }
        }
    }

    const totalPoint = computed(() => carts.value.reduce((total, item) => total + item.point * item.amount, 0))
    
    // const cartItemCounter = computed(() => carts.value.reduce((total, item) => total + item.amount, 0))
    const cartItemCounters = computed(() => {
        return carts.value.reduce((total, item) => total += item.amount, 0)
    })

    const removeItemCard = (rewardId) => {
        carts.value = carts.value.filter((item) => item.id !== rewardId)
    }

    const resetCart = () => {
        carts.value = []
    }


    return {

        addToCart,
        removeItemCard,
        resetCart,

        counterItems,
        cartItemCounters,
        totalPoint,

    }

})
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useRewardCartStore = defineStore('rewardCart', () => {
  const carts = ref([])
  const counterItems = ref([])

  const addToCart = (reward) => {
    const existingItem = carts.value.find((item) => item.id === reward.id)

    if (!existingItem) {
      carts.value.push({ ...reward, amount: 1 })

      counterItems.value.push({
        rewardID: reward.id,
        rewardName: reward.name,
        rewardPoint: reward.point,
        rewardAmount: 1,
      })
    } else {
      existingItem.amount += 1

      const counterItem = counterItems.value.find(item => item.rewardID === reward.id)
      if (counterItem) {
        counterItem.rewardAmount += 1
      }
    }
  }

  const totalPoint = computed(() =>
    carts.value.reduce((total, item) => total + item.point * item.amount, 0)
  )

  const cartItemCounters = computed(() =>
    carts.value.reduce((total, item) => total + item.amount, 0)
  )

  const removeItemCard = (rewardId) => {
    carts.value = carts.value.filter(item => item.id !== rewardId)
    counterItems.value = counterItems.value.filter(item => item.rewardID !== rewardId)
  }

  const resetCart = () => {
    carts.value = []
    counterItems.value = []
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

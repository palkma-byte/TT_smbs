<template>
  <main>
    <h1>This is the home page</h1>
    <item-list :itemsList="itemsList"></item-list>
  </main>
</template>

<script>
import ItemList from '../components/ItemList.vue';

export default {
  components: {
    ItemList
  },
  data() {
    return {
      itemsList: [
        { id: 1, name: "item1", price: "1000" },
        { id: 2, name: "item2", price: "2000" },
        { id: 3, name: "item3", price: "3000" }
      ]
    }
  }
}
</script>

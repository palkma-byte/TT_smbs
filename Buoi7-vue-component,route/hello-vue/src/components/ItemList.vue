<template>
  <div>
    <ul>
      <li v-for="item in itemsList" :key="item.id">
        Id: {{ item.id }} - Name: {{ item.name }} - Price: {{ item.price }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    itemsList: {
      type: Array,
      required: true
    }
  }
}
</script>

<template>
  <div>
    <p>Phương thức computed: {{ computedGreeting }}</p>
    <p>Phương thức method: {{ methodGreeting() }}</p>
    <input v-model="name" placeholder="Nhập tên của bạn">
  </div>
</template>

<script>
export default {
  data() {
    return {
      name: ''
    }
  },
  computed: {
    computedGreeting() {
      return `Hello, ${this.name}!`
    }
  },
  methods: {
    methodGreeting() {
      return `Hello, ${this.name}!`
    }
  }
}
</script>

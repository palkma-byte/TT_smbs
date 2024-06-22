<script>
export default {
  data() {
    return {
      tasks: []
    }
  },
  methods: {
    addTask() {
      if (this.task !== "") {
        this.tasks[this.tasks.length] = this.task.trim()
        this.task = ""
      }
      else alert("No input!")
    }
  }
}
</script>

<template>
  <h1>This is The Welcome page</h1>
  <input type="text" v-model="task">
  <button @click="addTask()">Add task</button>
  <ul>
    <ol v-for="task in tasks">---{{ task }}---</ol>
  </ul>
</template>
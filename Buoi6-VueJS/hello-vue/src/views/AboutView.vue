<script>
  export default {
    data(){
      return {
        likes: 0,
      }
    },
    methods:{
      like(){
        this.likes++;
      }
    }


  }
</script>

<template>
  
  <p v-if="likes > 0">
    this video has {{ likes }} likes 
    </p>

    <p v-else>nobody like it</p>
    <button @click="like()">Like</button>
</template>



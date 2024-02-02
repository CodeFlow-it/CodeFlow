<template>
  <div>
    <button class="button">
      <a href="http://localhost:8000/connect/github">{{ msg }}</a>
    </button>
  </div>
</template>
<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "ButtonComp",
  props: {
    msg: String,
    url: String,
  },
});
</script>
<style scoped>
.button {
  background-color: #4caf50;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 8px 2px;
  cursor: pointer;
  border: none;
  border-radius: 8px;
  transition-duration: 0.4s;
}

.button:hover {
  background-color: #45a049;
}
</style>

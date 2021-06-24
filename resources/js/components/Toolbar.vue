<template>
  <div class="toolbar">
    <button class="toolbar__btn toolbar__btn-delete" @click="deletePermit(permit.id)"><span class="material-icons-outlined md-24">clear</span></button>
    <button class="toolbar__btn toolbar__btn-edit"><span class="material-icons-outlined md-24">edit</span></button>
    <button class="toolbar__btn toolbar__btn-copy"><span class="material-icons-outlined md-24">content_copy</span></button>
  </div>
</template>

<script>
import { mapMutations } from "vuex";

export default {
  props: {
    permit: {
      type: Object,
      required: true,
    }
  },
  
  methods: {
    ...mapMutations('permit', ['deletePermit']),
  }
};
</script>

<style>
.toolbar {
  position: absolute;
  top: 2px;
  right: 2px;

  display: grid;
  grid-auto-flow: column;
  justify-items: center;
  align-items: center;
  gap: 5px;
}

.toolbar__btn {
  width: 40px;
  height: 35px;
  background-color: rgba(250, 240, 230, 0.6);
  border-radius: 4px;
  border: none;
  box-shadow: 0px 0px 2px rgba(0, 0, 0, .2);

  display: grid;
  justify-items: center;
  align-items: center;
  justify-content: center;

  transition: all .15s ease-in-out;
}

.toolbar__btn:hover {
  cursor: pointer;
  color: #fff;
}

.toolbar__btn-delete {
  color: #da251d;
  border: 1px solid #da251d;
}

.toolbar__btn-delete:hover {
  background: #da251d;
}

.toolbar__btn-edit {
  color: coral;
  border: 1px solid coral;
}

.toolbar__btn-edit:hover {
  background: coral;
}

.toolbar__btn-copy {
  color: #28a745;
  border: 1px solid #28a745;
}

.toolbar__btn-copy:hover {
  background: #28a745;
}
</style>

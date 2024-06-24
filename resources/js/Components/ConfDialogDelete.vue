<template>
	<Dialog v-model:visible="visible" modal header="Confirm Delete!" :style="{ width: '27rem' }">
        <template #header>
			<h5>Confirm Delete!</h5>
	    </template>
		{{ message }}
		<template #footer>
        	<Button  @click="onDelete()" label="Delete" class="p-button-danger" autofocus  />
		</template>
    </Dialog>
</template>
<script>
	export default {
		props:{
			visible:{
				type:Boolean,
				default:false
			},
            message:{
                type:String,
                default:null
            }
		},
        methods:{
            onClose(){
                this.$emit('update:visible', false);
            },
            onDelete(){
                this.$emit('onDelete');
            }
        }
	}
</script>
<template>
	<Dialog v-model:visible="visible" modal header="Information!" :style="{ width: '25rem' }">
        <template #header>
			<h4>Information</h4>
	    </template>
		{{ message }}
		<template #footer>
        	<Button label="Yes" icon="pi pi-check" autofocus @click="onApprove()"/>
		</template>
    </Dialog>
</template>
<script>
	export default {
		data(){
			return{
				percentage:80
			}
		},
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
            onApprove(){
                this.$emit('onApprove');
            }
        }
	}
</script>
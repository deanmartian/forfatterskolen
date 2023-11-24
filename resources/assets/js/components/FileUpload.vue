<template>
    <div>
		<div
			class="file-upload"
			@dragover.prevent="handleDragOver"
			@dragleave="handleDragLeave"
			@drop.prevent="handleDrop"
			@click="openFileInput"
			>
			<input
				ref="fileInput"
				type="file"
				class="hidden"
				:accept="accept"
				@change="handleFileChange"
				:multiple="multiple"
			/>
			<div v-if="!isDragging">
				<template v-if="files">
					{{ files.name }}
				</template>
				<template v-else>
					<i class="fa fa-cloud-upload-alt"></i>
					Dra og slipp filen eller <a href='javascript:void(0)' @click.stop="triggerFileInput">søk i maskinen</a>
				</template>
				
			</div>
			<div v-else>Drop a file here</div>
		</div>
  	</div>
</template>

<script>
export default {
	props: {
		defaultText: {
			type: String,
			default: "Drag and drop file or <a href='javascript:void(0)' v-on:click='openFileInput'>Browse</a>",
		},
		accept: {
			type: String,
			default: "image/*",
		},
		multiple: {
			type: Boolean,
			default: false,
		},
  	},
  	data() {
		return {
			isDragging: false,
			files: null,
		};
	},
	methods: {
		openFileInput() {
			this.$refs.fileInput.click();
		},
		triggerFileInput(event) {
			event.stopPropagation(); // Prevent event from reaching openFileInput
			this.openFileInput();
		},
		handleDragOver() {
			this.isDragging = true;
		},
		handleDragLeave() {
			this.isDragging = false;
		},
		handleDrop(event) {
			this.isDragging = false;
			const droppedFiles = event.dataTransfer.files;
			this.files = droppedFiles[0]; // Only store the first dropped file
		},
		handleFileChange(event) {
			const selectedFiles = event.target.files;
			this.files = selectedFiles[0]; // Only store the first selected file
			this.$emit("fileSelected", this.files); // Emit the event
		},
	},
};
</script>

<style>
.file-upload {
	border: 2px dashed #ccc;
  	padding: 20px;
  	text-align: center;
  	cursor: pointer;
}

.hidden {
  	display: none;
}
</style>
<template>
    <div class="row publishing-list">
        <div class="col-md-3">
            <h3 class="text-center">
                Liste over forfattere
            </h3>

            <div class="authors-list-container">
                <div class="align-items-center author-list-details" :class="{'active' : currentBook.id === book.id}"
                v-for="book in books" :key="book.id" @click="currentBook = book">
                    <div class="col-md-4 col-sm-3 image-container">
                        <img :src="book.author_image_jpg" :alt="book.title">
                    </div>
                    <div class="col-md-8 col-sm-9">
                        <h4>
                            {{ book.title }}
                        </h4>
                    </div>
                </div>
            </div>
        </div> <!-- end col-md-3 -->
        <div class="col-md-6">
            <div class="description-container">
                <h3 class="text-center">
                    {{ currentBook.title }}
                </h3>
                <div v-html="currentBook.description"></div>
            </div>
        </div> <!-- end col-md-6 -->

        <div class="col-md-3 text-center author-book-list-container">
            <h3>
                Bøker fra forfatter
            </h3>
            <div class="author-book-list">
                <template v-for="library in currentBook.libraries">
                    <a :href="library.book_link" v-if="library.book_link" :key="'library-' + library.id" 
                        target="_blank">
                        <img :src="library.book_image_jpg">
                    </a>
                    <img :src="library.book_image_jpg" :key="'library-' + library.id" v-else>
                </template>
                
            </div>
        </div> <!-- end col-md-3 -->
    </div>
</template>

<script>
export default {
    props: ['books'],
    data() {
        return {
            currentBook: {}
        }
    },
    mounted() {
        this.currentBook = this.books[0];
    }
}
</script>
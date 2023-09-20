<template>
    <div>
        <a :href="'/course/'+course.id" class="h1 font-weight-bold">
            {{ course.title }}
        </a>
        <img :src="course.course_image" class="w-100">

        <h3 class="mt-3 font-weight-bold">
            {{ trans('site.front.our-course.show.package-details-text') }}:
        </h3>
        <p v-html="coursePackage.description_with_check" class="mt-2 description-container"></p>
        <template
                v-if="coursePackage.has_coaching
                || (coursePackage.included_courses
                && coursePackage.included_courses.length)">

            <h4 class="mt-3">
                {{ trans('site.front.our-course.show.includes') }}: </h4>

            <template v-if="coursePackage.included_courses
            && coursePackage.included_courses.length">

                <template v-for="included_course in coursePackage.included_courses">
                    <p :key="included_course.id">
                        {{ included_course.included_package_course_title }}
                        ({{ included_course.included_package_variation }})
                    </p>
                    
                </template>

            </template>
        </template>
    </div>
</template>

<script>
export default {
    
    props: ['course', 'coursePackage'],

}
</script>

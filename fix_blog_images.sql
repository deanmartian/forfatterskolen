UPDATE blog SET image = REPLACE(image, 'blog/', 'blog-images/') WHERE image LIKE 'blog/%';

<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tmdb_id = $_POST['tmdb_id'];
    $json = $_POST['json'];
    $embed_source = $_POST['embed_source'];
    $release_year = $_POST['release_year'];

    if (addMovie($tmdb_id, $json, $embed_source, $release_year)) {
        echo "Movie added successfully!";
    } else {
        echo "Failed to add movie.";
    }
}

$movies = fetchAllMovies();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Admin Panel</h1>

        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Add Movie</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="tmdb_id" class="block text-sm font-medium">TMDB ID</label>
                    <input type="text" id="tmdb_id" name="tmdb_id" class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                </div>
                <div>
                    <label for="json" class="block text-sm font-medium">JSON Data</label>
                    <textarea id="json" name="json" rows="4" class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"></textarea>
                </div>
                <div>
                    <label for="embed_source" class="block text-sm font-medium">Embed Source</label>
                    <input type="text" id="embed_source" name="embed_source" class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                </div>
                <div>
                    <label for="release_year" class="block text-sm font-medium">Release Year</label>
                    <input type="text" id="release_year" name="release_year" class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                </div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">Add Movie</button>
            </form>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Search Movies</h2>
            <input type="text" id="search" class="mt-1 block w-full bg-gray-800 border-gray-700 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Search for movies...">
            <div id="search-results" class="mt-4 space-y-4"></div>
        </div>

        <div>
            <h2 class="text-2xl font-semibold mb-4">All Movies</h2>
            <ul class="space-y-4">
                <?php foreach ($movies as $movie): ?>
                    <li class="bg-gray-800 p-4 rounded-md">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($movie['tmdb_id']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['json']); ?></p>
                        <p>Embed Source: <?php echo htmlspecialchars($movie['embed_source']); ?></p>
                        <p>Release Year: <?php echo htmlspecialchars($movie['release_year']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        document.getElementById('search').addEventListener('input', async function() {
            const query = this.value;
            if (query.length < 3) {
                document.getElementById('search-results').innerHTML = '';
                return;
            }

            try {
                const response = await axios.get(`https://api.themoviedb.org/3/search/movie?api_key=YOUR_TMDB_API_KEY&query=${query}`);
                const movies = response.data.results;
                const resultsContainer = document.getElementById('search-results');
                resultsContainer.innerHTML = '';

                movies.forEach(movie => {
                    const movieElement = document.createElement('div');
                    movieElement.classList.add('bg-gray-800', 'p-4', 'rounded-md');
                    movieElement.innerHTML = `
                        <h3 class="text-xl font-bold">${movie.title}</h3>
                        <p>${movie.overview}</p>
                        <button class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded" onclick="addMovie(${movie.id}, '${movie.title}', '${movie.release_date.split('-')[0]}')">Add Movie</button>
                    `;
                    resultsContainer.appendChild(movieElement);
                });
            } catch (error) {
                console.error('Error fetching movies:', error);
            }
        });

        async function addMovie(tmdb_id, title, release_year) {
            try {
                const response = await axios.get(`https://api.themoviedb.org/3/movie/${tmdb_id}?api_key=YOUR_TMDB_API_KEY`);
                const movie = response.data;
                const json = JSON.stringify(movie);

                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="tmdb_id" value="${tmdb_id}">
                    <input type="hidden" name="json" value='${json}'>
                    <input type="hidden" name="embed_source" value="default">
                    <input type="hidden" name="release_year" value="${release_year}">
                `;
                document.body.appendChild(form);
                form.submit();
            } catch (error) {
                console.error('Error adding movie:', error);
            }
        }
    </script>
</body>
</html>

<?php
function getYouTubeVideoData($url) {
    // Get YouTube video ID from the URL
    preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^&\n]{11})/', $url, $matches);

    if (empty($matches)) {
        return null; // Invalid URL
    }

    $videoId = $matches[1];
    $embedUrl = "https://www.youtube.com/oembed?url=$url&format=json";

    // Make API request
    $response = file_get_contents($embedUrl);
    $data = json_decode($response, true);

    if ($data && isset($data['title']) && isset($data['author_name'])) {
        return [
            'thumbnail' => "https://img.youtube.com/vi/$videoId/default.jpg",
            'title' => $data['title'],
            'author' => $data['author_name'],
            'videoUrl' => $url
        ];
    }

    return null; // Video not found
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['youtube_url'])) {
    $youtubeUrl = $_POST['youtube_url'];
    $videoData = getYouTubeVideoData($youtubeUrl);

    if ($videoData) {
        echo "<a href='{$videoData['videoUrl']}' target='_blank'>";
        echo "<img src='{$videoData['thumbnail']}' alt='Thumbnail' />";
        echo "</a>";
        echo "<h3>{$videoData['title']}</h3>";
        echo "<p>Author: {$videoData['author']}</p>";
    } else {
        echo "Invalid YouTube URL or video not found.";
    }
}
?>

<form method="POST" action="">
    <label for="youtube_url">Enter YouTube URL:</label>
    <input type="text" id="youtube_url" name="youtube_url" required>
    <button type="submit">Get Video Info</button>
</form>
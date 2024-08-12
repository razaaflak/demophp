<!DOCTYPE html>
<html>
<head>
    <title>LinkedIn Posts Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }
        input[type="submit"] {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            background-color: #0073b1;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        .post {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .post img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .post-header img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }
        .post-header h2 {
            margin: 0;
            font-size: 18px;
        }
        .post-header p {
            margin: 0;
            color: #777;
        }
        .post-text {
            margin-bottom: 15px;
        }
        .post-footer {
            color: #777;
        }
    </style>
</head>
<body>
    <h1>LinkedIn Posts Search</h1>
    <form method="post">
        <label for="keyword">Enter a keyword:</label>
        <input type="text" id="keyword" name="keyword" required>
        <input type="submit" value="Search">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://fresh-linkedin-profile-data.p.rapidapi.com/search-posts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'search_keywords' => $_POST['keyword'],
                'date_posted' => '',
                'content_type' => 'Images',
                'from_member' => '',
                'from_company' => '',
                'mentioning_member' => '',
                'author_company' => '',
                'author_industry' => '',
                'author_keyword' => '',
                'page' => 1
            ]),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "x-rapidapi-host: fresh-linkedin-profile-data.p.rapidapi.com",
                "x-rapidapi-key: b1aa826370msh028a8b9eef08fe1p1f83eejsne10754fecb66"
            ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $data = json_decode($response, true);
            
            if (isset($data['data']) && is_array($data['data'])) {
                // Sort posts by num_likes in descending order
                usort($data['data'], function($a, $b) {
                    return $b['num_likes'] - $a['num_likes'];
                });
                
                foreach ($data['data'] as $post) {
                    echo '<div class="post">';
                    echo '<div class="post-header">';
                    echo '<img src="https://via.placeholder.com/50" alt="Poster Image">'; // Placeholder image
                    echo '<div>';
                    echo '<h2><a href="'. htmlspecialchars($post['poster_linkedin_url']) .'" target="_blank">'. htmlspecialchars($post['poster_name']) .'</a></h2>';
                    echo '<p>'. htmlspecialchars($post['poster_title']) .'</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="post-text">'. htmlspecialchars($post['text']) .'</div>';
                    echo '<a href="'. htmlspecialchars($post['post_url']) .'" target="_blank"><img src="https://via.placeholder.com/600x400" alt="Post Image"></a>'; // Placeholder image
                    echo '<div class="post-footer">';
                    echo '<p>Likes: '. htmlspecialchars($post['num_likes']) .'</p>';
                    echo '<p>Comments: '. htmlspecialchars($post['num_comments']) .'</p>';
                    echo '<p>Shares: '. htmlspecialchars($post['num_shares']) .'</p>';
                    echo '<p>Posted on: '. htmlspecialchars($post['posted']) .'</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo 'No posts found.';
            }
        }
    }
    ?>
</body>
</html>

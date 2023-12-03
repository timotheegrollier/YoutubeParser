<?php

namespace App\Twig;

use App\Repository\YoutubeRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use RicardoFiorani\Matcher\VideoServiceMatcher;

class YoutubeExtension extends AbstractExtension
{

    private $youtubeParser;
    private $youtubeRepo;

    public function __construct(YoutubeRepository $youtubeRepository)
    {
        $this->youtubeParser = new VideoServiceMatcher();
        $this->youtubeRepo = $youtubeRepository;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('youtube_thumbnail', [$this, 'youtubeThumbnail']),
            new TwigFilter('youtube_view_count',[$this,'viewCount']),
            new TwigFilter('youtube_player', [$this, 'youtubePlayer']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('video_count',[$this,'videoCount']),
        ];
    }

    public function youtubeThumbnail($value)
    {
        $video = $this->youtubeParser->parse($value);
        return $video->getLargestThumbnail();
    }

    public function youtubePlayer($value)
    {
        $video = $this->youtubeParser->parse($value);
        return $video->getEmbedCode('100%', 400, true, true);
    }


    public function videoCount(){
        return $this->youtubeRepo->countAll();
    }

    public function viewCount($url) {
        $apiKey = $_ENV["YT_API"];

        $videoId = $this->getVideoId($url);
        

        $url = "https://www.googleapis.com/youtube/v3/videos?part=statistics&id={$videoId}&key={$apiKey}";
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $data = json_decode($response, true);
    
    
        if (isset($data['items'][0]['statistics']['viewCount'])) {
            return $data['items'][0]['statistics']['viewCount'];
        } else {
            return "Nombre de vues non disponible";
        }
    }



    public function getVideoId(string $url): string
    {
    
        return $this->youtubeParser->parse($url)->videoId;
    }

}
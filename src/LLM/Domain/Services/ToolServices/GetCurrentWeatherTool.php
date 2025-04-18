<?php

declare(strict_types=1);

namespace Src\LLM\Domain\Services\ToolServices;


use Src\LLM\Domain\Tools\ToolFunctionInterface;

class GetCurrentWeatherTool implements ToolFunctionInterface
{

    public function name(): string
    {
        return 'get_weather_tool';
    }

    public function description(): string
    {
        return "Get the current weather for a location";
    }

    public function definition(): array
    {
        return [
            "type" => "function",
            "function" => [
                "name" => $this->name(),
                "description" => $this->description(),
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "location" => [
                            "type" => "string",
                            "description" => "The location to get the weather for, e.g. San Francisco, CA",
                        ],
                        "format" => [
                            "type" => "string",
                            "description" => "The format to return the weather in, e.g. 'celsius' or 'fahrenheit'",
                            "enum" => ["celsius", "fahrenheit"],
                        ],
                    ],
                    "required" => ["location", "format"],
                ],
            ],
        ];
    }

    public function execute(mixed $arguments = null): array
    {
        $location = $arguments['location'];
        $format = $arguments['format'];

        $url = "https://wttr.in/" . urlencode($location) . "?format=j1";
        $response = @file_get_contents($url);

        if ($response === false) {
            return ['error' => 'Could not fetch weather data'];
        }

        $data = json_decode($response, true);
        $current = $data['current_condition'][0];

        return [
            'location' => $location,
            'temperature' => $format === 'fahrenheit'
                ? $current['temp_F'] . "°F"
                : $current['temp_C'] . "°C",
            'description' => $current['weatherDesc'][0]['value'],
            'humidity' => $current['humidity'] . '%',
            'wind_speed' => $current['windspeedKmph'] . ' km/h',
            'feels_like' => $format === 'fahrenheit'
                ? $current['FeelsLikeF'] . "°F"
                : $current['FeelsLikeC'] . "°C",
        ];
    }

}

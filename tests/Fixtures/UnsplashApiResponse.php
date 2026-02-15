<?php

namespace Mansoor\UnsplashPicker\Tests\Fixtures;

class UnsplashApiResponse
{
    public static function photo(array $overrides = []): array
    {
        return array_merge([
            'id' => 'abc123',
            'slug' => 'a-beautiful-landscape-abc123',
            'created_at' => '2024-01-15T10:30:00-05:00',
            'updated_at' => '2024-06-10T12:00:00-05:00',
            'promoted_at' => '2024-01-16T08:15:00-05:00',
            'width' => 4000,
            'height' => 3000,
            'color' => '#A7A2A1',
            'blur_hash' => 'LaLXMa9Fx[D%~q%MtQM|kDRjtRIU',
            'description' => 'A beautiful landscape photo.',
            'alt_description' => 'green mountains under blue sky',
            'likes' => 286,
            'liked_by_user' => false,
            'current_user_collections' => [],
            'sponsorship' => null,
            'topic_submissions' => (object) [],
            'urls' => [
                'raw' => 'https://images.unsplash.com/photo-abc123?ixlib=rb-4.0.3',
                'full' => 'https://images.unsplash.com/photo-abc123?ixlib=rb-4.0.3&q=75&fm=jpg',
                'regular' => 'https://images.unsplash.com/photo-abc123?ixlib=rb-4.0.3&q=75&fm=jpg&w=1080&fit=max',
                'small' => 'https://images.unsplash.com/photo-abc123?ixlib=rb-4.0.3&q=75&fm=jpg&w=400&fit=max',
                'thumb' => 'https://images.unsplash.com/photo-abc123?ixlib=rb-4.0.3&q=75&fm=jpg&w=200&fit=max',
            ],
            'links' => [
                'self' => 'https://api.unsplash.com/photos/abc123',
                'html' => 'https://unsplash.com/photos/abc123',
                'download' => 'https://unsplash.com/photos/abc123/download',
                'download_location' => 'https://api.unsplash.com/photos/abc123/download?ixid=MnwxMjA3fDB8MHx',
            ],
            'user' => [
                'id' => 'user123',
                'updated_at' => '2024-06-10T12:00:00-05:00',
                'username' => 'johndoe',
                'name' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'twitter_username' => 'johndoe',
                'instagram_username' => 'johndoe',
                'portfolio_url' => 'https://johndoe.com/',
                'bio' => 'Photographer based in New York',
                'location' => 'New York',
                'total_likes' => 52,
                'total_photos' => 124,
                'total_collections' => 13,
                'accepted_tos' => true,
                'for_hire' => true,
                'social' => [
                    'instagram_username' => 'johndoe',
                    'portfolio_url' => 'https://johndoe.com/',
                    'twitter_username' => 'johndoe',
                    'paypal_email' => null,
                ],
                'profile_image' => [
                    'small' => 'https://images.unsplash.com/profile-user123?fit=crop&h=32&w=32',
                    'medium' => 'https://images.unsplash.com/profile-user123?fit=crop&h=64&w=64',
                    'large' => 'https://images.unsplash.com/profile-user123?fit=crop&h=128&w=128',
                ],
                'links' => [
                    'self' => 'https://api.unsplash.com/users/johndoe',
                    'html' => 'https://unsplash.com/@johndoe',
                    'photos' => 'https://api.unsplash.com/users/johndoe/photos',
                    'likes' => 'https://api.unsplash.com/users/johndoe/likes',
                    'portfolio' => 'https://api.unsplash.com/users/johndoe/portfolio',
                    'following' => 'https://api.unsplash.com/users/johndoe/following',
                    'followers' => 'https://api.unsplash.com/users/johndoe/followers',
                ],
            ],
        ], $overrides);
    }

    public static function photos(int $count = 3, array $photoOverrides = []): array
    {
        $photos = [];

        for ($i = 0; $i < $count; $i++) {
            $id = 'photo_' . ($i + 1);
            $photos[] = self::photo(array_merge([
                'id' => $id,
                'slug' => "photo-{$id}",
                'description' => "Photo {$id} description",
                'urls' => [
                    'raw' => "https://images.unsplash.com/{$id}?ixlib=rb-4.0.3",
                    'full' => "https://images.unsplash.com/{$id}?ixlib=rb-4.0.3&q=75&fm=jpg",
                    'regular' => "https://images.unsplash.com/{$id}?ixlib=rb-4.0.3&q=75&fm=jpg&w=1080&fit=max",
                    'small' => "https://images.unsplash.com/{$id}?ixlib=rb-4.0.3&q=75&fm=jpg&w=400&fit=max",
                    'thumb' => "https://images.unsplash.com/{$id}?ixlib=rb-4.0.3&q=75&fm=jpg&w=200&fit=max",
                ],
                'links' => [
                    'self' => "https://api.unsplash.com/photos/{$id}",
                    'html' => "https://unsplash.com/photos/{$id}",
                    'download' => "https://unsplash.com/photos/{$id}/download",
                    'download_location' => "https://api.unsplash.com/photos/{$id}/download?ixid=test",
                ],
                'user' => [
                    'id' => "user_{$i}",
                    'updated_at' => '2024-06-10T12:00:00-05:00',
                    'username' => "photographer_{$i}",
                    'name' => "Photographer {$i}",
                    'first_name' => 'Photographer',
                    'last_name' => (string) $i,
                    'twitter_username' => null,
                    'instagram_username' => null,
                    'portfolio_url' => null,
                    'bio' => null,
                    'location' => null,
                    'total_likes' => 10 + $i,
                    'total_photos' => 50 + $i,
                    'total_collections' => 5 + $i,
                    'accepted_tos' => true,
                    'for_hire' => false,
                    'social' => [
                        'instagram_username' => null,
                        'portfolio_url' => null,
                        'twitter_username' => null,
                        'paypal_email' => null,
                    ],
                    'profile_image' => [
                        'small' => "https://images.unsplash.com/profile-user_{$i}?fit=crop&h=32&w=32",
                        'medium' => "https://images.unsplash.com/profile-user_{$i}?fit=crop&h=64&w=64",
                        'large' => "https://images.unsplash.com/profile-user_{$i}?fit=crop&h=128&w=128",
                    ],
                    'links' => [
                        'self' => "https://api.unsplash.com/users/photographer_{$i}",
                        'html' => "https://unsplash.com/@photographer_{$i}",
                        'photos' => "https://api.unsplash.com/users/photographer_{$i}/photos",
                        'likes' => "https://api.unsplash.com/users/photographer_{$i}/likes",
                        'portfolio' => "https://api.unsplash.com/users/photographer_{$i}/portfolio",
                        'following' => "https://api.unsplash.com/users/photographer_{$i}/following",
                        'followers' => "https://api.unsplash.com/users/photographer_{$i}/followers",
                    ],
                ],
            ], $photoOverrides));
        }

        return $photos;
    }

    public static function searchResponse(int $photoCount = 3, int $total = 100, int $totalPages = 5): array
    {
        return [
            'total' => $total,
            'total_pages' => $totalPages,
            'results' => self::photos($photoCount),
        ];
    }

    public static function emptySearchResponse(): array
    {
        return [
            'total' => 0,
            'total_pages' => 0,
            'results' => [],
        ];
    }

    public static function errorResponse(string $message = 'OAuth error: The access token is invalid'): array
    {
        return [
            'errors' => [$message],
        ];
    }

    public static function rateLimitErrorResponse(): array
    {
        return [
            'errors' => ['Rate Limit Exceeded'],
        ];
    }
}

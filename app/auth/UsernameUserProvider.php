<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class UsernameUserProvider extends EloquentUserProvider
{
    public function __construct(HasherContract $hasher, string $model)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * Retrieve user by credentials — lookup by 'username' bukan 'email'
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $credentials = array_filter(
            $credentials,
            fn($key) => !str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($credentials)) {
            return null;
        }

        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * Validate credentials — pastikan password cocok
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $this->hasher->check(
            $credentials['password'],
            $user->getAuthPassword()
        );
    }
}
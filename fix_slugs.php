<?php
echo "Updating User slugs...\n";
\App\Models\User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Generate slug using the boot method logic
        $user->slug = null; // force empty to trigger boot method logic
        $user->save();
        echo "User {$user->id} slug: {$user->slug}\n";
    }
});

echo "Updating Listing slugs...\n";
\App\Models\Listing::chunk(100, function ($listings) {
    foreach ($listings as $listing) {
        $listing->slug = null; // force empty
        $listing->save();
        echo "Listing {$listing->id} slug: {$listing->slug}\n";
    }
});
echo "Done.\n";

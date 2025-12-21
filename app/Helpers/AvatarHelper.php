<?php

namespace App\Helpers;

class AvatarHelper
{
    /**
     * Generate avatar URL berdasarkan nama user
     * Menggunakan UI Avatars API untuk generate avatar otomatis
     */
    public static function generate($name, $size = 100, $background = 'f97316', $color = 'ffffff')
    {
        // Ambil inisial dari nama (2 huruf pertama)
        $initials = self::getInitials($name);
        
        // Generate avatar menggunakan UI Avatars API
        $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($initials) . 
                     "&size={$size}" . 
                     "&background={$background}" . 
                     "&color={$color}" . 
                     "&bold=true" . 
                     "&format=png";
        
        return $avatarUrl;
    }
    
    /**
     * Get avatar URL untuk user
     * Prioritas: 1. Uploaded avatar, 2. Generated avatar
     */
    public static function getAvatarUrl($userId, $userName, $role = 'admin')
    {
        // Cek apakah ada avatar yang di-upload
        $rel = 'avatars/user_'.$userId.'.jpg';
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($rel)) {
            return asset('storage/'.$rel);
        }
        
        // Generate avatar otomatis berdasarkan nama
        // Warna berbeda untuk admin dan super admin
        $background = $role === 'super' ? 'ec4899' : 'f97316'; // Pink untuk super, Orange untuk admin
        return self::generate($userName, 100, $background, 'ffffff');
    }
    
    /**
     * Ambil inisial dari nama (maksimal 2 huruf)
     */
    private static function getInitials($name)
    {
        $name = trim($name);
        if (empty($name)) {
            return 'U';
        }
        
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            // Ambil huruf pertama dari kata pertama dan kedua
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            // Ambil 2 huruf pertama dari nama
            return strtoupper(substr($name, 0, 2));
        }
    }
}


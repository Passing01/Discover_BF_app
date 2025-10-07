<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Site;
use App\Models\SiteBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SiteManagerProfileController extends Controller
{
    /**
     * Affiche le tableau de bord du gestionnaire de sites
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Statistiques pour le tableau de bord
        $stats = [
            'total_sites' => $user->managedSites()->count(),
            'active_sites' => $user->managedSites()->where('is_active', true)->count(),
            'total_bookings' => SiteBooking::whereHas('site', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            })->count(),
            'pending_bookings' => SiteBooking::whereHas('site', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            })->where('status', 'pending')->count(),
            'revenue' => SiteBooking::whereHas('site', function($q) use ($user) {
                return $q->where('manager_id', $user->id);
            })->where('status', 'completed')
              ->whereMonth('created_at', now()->month)
              ->sum('total_amount'),
            'total_visitors' => SiteBooking::whereHas('site', function($q) use ($user) {
                return $q->where('manager_id', $user->id);
            })->where('visit_date', '>=', now()->subDays(30))
              ->sum('visitors_count')
        ];
        
        // Dernières réservations
        $recentBookings = SiteBooking::whereHas('site', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            })
            ->with(['site', 'user'])
            ->latest()
            ->take(5)
            ->get();
            
        // Derniers sites ajoutés
        $recentSites = $user->managedSites()
            ->latest()
            ->take(5)
            ->get();
            
        // Récupérer tous les sites pour le gestionnaire
        $sites = $user->managedSites()->get();
        
        return view('site-manager.dashboard', compact('stats', 'recentBookings', 'recentSites', 'user', 'sites'));
    }
    
    /**
     * Affiche le formulaire d'édition du profil
     */
    public function edit()
    {
        $user = auth()->user();
        $countries = [
            'AF' => 'Afghanistan',
            'ZA' => 'Afrique du Sud',
            'AL' => 'Albanie',
            'DZ' => 'Algérie',
            'DE' => 'Allemagne',
            'AD' => 'Andorre',
            'AO' => 'Angola',
            'AG' => 'Antigua-et-Barbuda',
            'SA' => 'Arabie saoudite',
            'AR' => 'Argentine',
            'AM' => 'Arménie',
            'AU' => 'Australie',
            'AT' => 'Autriche',
            'AZ' => 'Azerbaïdjan',
            'BS' => 'Bahamas',
            'BH' => 'Bahreïn',
            'BD' => 'Bangladesh',
            'BB' => 'Barbade',
            'BE' => 'Belgique',
            'BZ' => 'Belize',
            'BJ' => 'Bénin',
            'BT' => 'Bhoutan',
            'BY' => 'Biélorussie',
            'MM' => 'Birmanie',
            'BO' => 'Bolivie',
            'BA' => 'Bosnie-Herzégovine',
            'BW' => 'Botswana',
            'BR' => 'Brésil',
            'BN' => 'Brunei',
            'BG' => 'Bulgarie',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodge',
            'CM' => 'Cameroun',
            'CA' => 'Canada',
            'CV' => 'Cap-Vert',
            'CL' => 'Chili',
            'CN' => 'Chine',
            'CY' => 'Chypre',
            'CO' => 'Colombie',
            'KM' => 'Comores',
            'CG' => 'Congo-Brazzaville',
            'CD' => 'Congo-Kinshasa',
            'KP' => 'Corée du Nord',
            'KR' => 'Corée du Sud',
            'CR' => 'Costa Rica',
            'CI' => 'Côte d\'Ivoire',
            'HR' => 'Croatie',
            'CU' => 'Cuba',
            'DK' => 'Danemark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominique',
            'EG' => 'Égypte',
            'AE' => 'Émirats arabes unis',
            'EC' => 'Équateur',
            'ER' => 'Érythrée',
            'ES' => 'Espagne',
            'EE' => 'Estonie',
            'SZ' => 'Eswatini',
            'VA' => 'État de la Cité du Vatican',
            'US' => 'États-Unis',
            'ET' => 'Éthiopie',
            'FJ' => 'Fidji',
            'FI' => 'Finlande',
            'FR' => 'France',
            'GA' => 'Gabon',
            'GM' => 'Gambie',
            'GE' => 'Géorgie',
            'GH' => 'Ghana',
            'GR' => 'Grèce',
            'GD' => 'Grenade',
            'GT' => 'Guatemala',
            'GN' => 'Guinée',
            'GQ' => 'Guinée équatoriale',
            'GW' => 'Guinée-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haïti',
            'HN' => 'Honduras',
            'HU' => 'Hongrie',
            'MH' => 'Îles Marshall',
            'SB' => 'Îles Salomon',
            'IN' => 'Inde',
            'ID' => 'Indonésie',
            'IQ' => 'Irak',
            'IR' => 'Iran',
            'IE' => 'Irlande',
            'IS' => 'Islande',
            'IL' => 'Israël',
            'IT' => 'Italie',
            'JM' => 'Jamaïque',
            'JP' => 'Japon',
            'JO' => 'Jordanie',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KG' => 'Kirghizistan',
            'KI' => 'Kiribati',
            'KW' => 'Koweït',
            'LA' => 'Laos',
            'LS' => 'Lesotho',
            'LV' => 'Lettonie',
            'LB' => 'Liban',
            'LR' => 'Libéria',
            'LY' => 'Libye',
            'LI' => 'Liechtenstein',
            'LT' => 'Lituanie',
            'LU' => 'Luxembourg',
            'MK' => 'Macédoine du Nord',
            'MG' => 'Madagascar',
            'MY' => 'Malaisie',
            'MW' => 'Malawi',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malte',
            'MA' => 'Maroc',
            'MU' => 'Maurice',
            'MR' => 'Mauritanie',
            'MX' => 'Mexique',
            'FM' => 'Micronésie',
            'MD' => 'Moldavie',
            'MC' => 'Monaco',
            'MN' => 'Mongolie',
            'ME' => 'Monténégro',
            'MZ' => 'Mozambique',
            'NA' => 'Namibie',
            'NR' => 'Nauru',
            'NP' => 'Népal',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigéria',
            'NO' => 'Norvège',
            'NZ' => 'Nouvelle-Zélande',
            'OM' => 'Oman',
            'UG' => 'Ouganda',
            'UZ' => 'Ouzbékistan',
            'PK' => 'Pakistan',
            'PW' => 'Palaos',
            'PA' => 'Panama',
            'PG' => 'Papouasie-Nouvelle-Guinée',
            'PY' => 'Paraguay',
            'NL' => 'Pays-Bas',
            'PE' => 'Pérou',
            'PH' => 'Philippines',
            'PL' => 'Pologne',
            'PT' => 'Portugal',
            'CF' => 'République centrafricaine',
            'DO' => 'République dominicaine',
            'CZ' => 'République tchèque',
            'RO' => 'Roumanie',
            'GB' => 'Royaume-Uni',
            'RU' => 'Russie',
            'RW' => 'Rwanda',
            'KN' => 'Saint-Christophe-et-Niévès',
            'SM' => 'Saint-Marin',
            'VC' => 'Saint-Vincent-et-les-Grenadines',
            'LC' => 'Sainte-Lucie',
            'SV' => 'Salvador',
            'WS' => 'Samoa',
            'ST' => 'Sao Tomé-et-Principe',
            'SN' => 'Sénégal',
            'RS' => 'Serbie',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapour',
            'SK' => 'Slovaquie',
            'SI' => 'Slovénie',
            'SO' => 'Somalie',
            'SD' => 'Soudan',
            'SS' => 'Soudan du Sud',
            'LK' => 'Sri Lanka',
            'SE' => 'Suède',
            'CH' => 'Suisse',
            'SR' => 'Suriname',
            'SY' => 'Syrie',
            'TJ' => 'Tadjikistan',
            'TZ' => 'Tanzanie',
            'TD' => 'Tchad',
            'TH' => 'Thaïlande',
            'TL' => 'Timor oriental',
            'TG' => 'Togo',
            'TO' => 'Tonga',
            'TT' => 'Trinité-et-Tobago',
            'TN' => 'Tunisie',
            'TM' => 'Turkménistan',
            'TR' => 'Turquie',
            'TV' => 'Tuvalu',
            'UA' => 'Ukraine',
            'UY' => 'Uruguay',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viêt Nam',
            'YE' => 'Yémen',
            'ZM' => 'Zambie',
            'ZW' => 'Zimbabwe',
            'BF' => 'Burkina Faso',
            'BJ' => 'Bénin',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'CM' => 'Cameroun',
            'CV' => 'Cap-Vert',
            'CF' => 'République centrafricaine',
            'TD' => 'Tchad',
            'KM' => 'Comores',
            'CG' => 'Congo-Brazzaville',
            'CD' => 'Congo-Kinshasa',
            'CI' => 'Côte d\'Ivoire',
            'DJ' => 'Djibouti',
            'EG' => 'Égypte',
            'GQ' => 'Guinée équatoriale',
            'ER' => 'Érythrée',
            'SZ' => 'Eswatini',
            'ET' => 'Éthiopie',
            'GA' => 'Gabon',
            'GM' => 'Gambie',
            'GH' => 'Ghana',
            'GN' => 'Guinée',
            'GW' => 'Guinée-Bissau',
            'KE' => 'Kenya',
            'LS' => 'Lesotho',
            'LR' => 'Libéria',
            'LY' => 'Libye',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'ML' => 'Mali',
            'MR' => 'Mauritanie',
            'MU' => 'Maurice',
            'MA' => 'Maroc',
            'MZ' => 'Mozambique',
            'NA' => 'Namibie',
            'NE' => 'Niger',
            'NG' => 'Nigéria',
            'RW' => 'Rwanda',
            'ST' => 'Sao Tomé-et-Principe',
            'SN' => 'Sénégal',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SO' => 'Somalie',
            'ZA' => 'Afrique du Sud',
            'SS' => 'Soudan du Sud',
            'SD' => 'Soudan',
            'TZ' => 'Tanzanie',
            'TG' => 'Togo',
            'TN' => 'Tunisie',
            'UG' => 'Ouganda',
            'ZM' => 'Zambie',
            'ZW' => 'Zimbabwe'
        ];
        
        return view('site-manager.profile.edit', compact('user', 'countries'));
    }

    /**
     * Met à jour le profil de l'utilisateur
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 
                        Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048']
        ]);
        
        // Mise à jour de la photo de profil si fournie
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->profile_photo_path) {
                Storage::delete('public/' . $user->profile_photo_path);
            }
            
            // Enregistrer la nouvelle photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $validated['profile_photo_path'] = $path;
        }
        
        // Mise à jour des informations de l'utilisateur
        $user->update($validated);
        
        return redirect()->route('site-manager.profile.edit')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }
    
    /**
     * Met à jour le mot de passe de l'utilisateur
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();
        
        return back()->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }
    
}

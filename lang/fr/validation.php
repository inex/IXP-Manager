<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Missing keys fall back to lang/en/validation.php (see 'fallback_locale'
    | in config/app.php), so an untranslated rule shows its English message
    | rather than breaking. This file covers the rules IXP Manager's form
    | requests actually use; add more as needed.
    |
    | :attribute is replaced with the field name - see 'attributes' at the
    | bottom of this file - so it must be kept verbatim in every message.
    |
    */

    'accepted'              => 'Le champ :attribute doit être accepté.',
    'active_url'            => "Le champ :attribute n'est pas une URL valide.",
    'after'                 => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal'        => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha'                 => 'Le champ :attribute ne peut contenir que des lettres.',
    'alpha_dash'            => 'Le champ :attribute ne peut contenir que des lettres, des chiffres, des tirets et des tirets bas.',
    'alpha_num'             => 'Le champ :attribute ne peut contenir que des lettres et des chiffres.',
    'array'                 => 'Le champ :attribute doit être un tableau.',
    'before'                => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal'       => 'Le champ :attribute doit être une date antérieure ou égale au :date.',

    'between'               => [
        'numeric'   => 'La valeur de :attribute doit être comprise entre :min et :max.',
        'file'      => 'Le fichier :attribute doit avoir une taille comprise entre :min et :max kilo-octets.',
        'string'    => 'Le texte :attribute doit comporter entre :min et :max caractères.',
        'array'     => 'Le champ :attribute doit comporter entre :min et :max éléments.',
    ],

    'boolean'               => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed'             => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password'      => 'Le mot de passe est incorrect.',
    'date'                  => "Le champ :attribute n'est pas une date valide.",
    'date_equals'           => 'Le champ :attribute doit être une date égale au :date.',
    'date_format'           => 'Le champ :attribute ne correspond pas au format :format.',
    'different'             => 'Les champs :attribute et :other doivent être différents.',
    'digits'                => 'Le champ :attribute doit comporter :digits chiffres.',
    'digits_between'        => 'Le champ :attribute doit comporter entre :min et :max chiffres.',
    'email'                 => 'Le champ :attribute doit être une adresse e-mail valide.',
    'ends_with'             => 'Le champ :attribute doit se terminer par une des valeurs suivantes : :values.',
    'exists'                => 'La valeur sélectionnée pour :attribute est invalide.',
    'file'                  => 'Le champ :attribute doit être un fichier.',
    'filled'                => 'Le champ :attribute doit avoir une valeur.',

    'gt'                    => [
        'numeric'   => 'La valeur de :attribute doit être supérieure à :value.',
        'file'      => 'Le fichier :attribute doit être plus grand que :value kilo-octets.',
        'string'    => 'Le texte :attribute doit comporter plus de :value caractères.',
        'array'     => 'Le champ :attribute doit comporter plus de :value éléments.',
    ],

    'gte'                   => [
        'numeric'   => 'La valeur de :attribute doit être supérieure ou égale à :value.',
        'file'      => 'Le fichier :attribute doit avoir une taille supérieure ou égale à :value kilo-octets.',
        'string'    => 'Le texte :attribute doit comporter au moins :value caractères.',
        'array'     => 'Le champ :attribute doit comporter au moins :value éléments.',
    ],

    'image'                 => 'Le champ :attribute doit être une image.',
    'in'                    => 'La valeur sélectionnée pour :attribute est invalide.',
    'in_array'              => "La valeur du champ :attribute n'existe pas dans :other.",
    'integer'               => 'Le champ :attribute doit être un nombre entier.',
    'ip'                    => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4'                  => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6'                  => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json'                  => 'Le champ :attribute doit être un document JSON valide.',

    'lt'                    => [
        'numeric'   => 'La valeur de :attribute doit être inférieure à :value.',
        'file'      => 'Le fichier :attribute doit être plus petit que :value kilo-octets.',
        'string'    => 'Le texte :attribute doit comporter moins de :value caractères.',
        'array'     => 'Le champ :attribute doit comporter moins de :value éléments.',
    ],

    'lte'                   => [
        'numeric'   => 'La valeur de :attribute doit être inférieure ou égale à :value.',
        'file'      => 'Le fichier :attribute doit avoir une taille inférieure ou égale à :value kilo-octets.',
        'string'    => 'Le texte :attribute doit comporter au plus :value caractères.',
        'array'     => 'Le champ :attribute doit comporter au plus :value éléments.',
    ],

    'max'                   => [
        'numeric'   => 'La valeur de :attribute ne peut pas être supérieure à :max.',
        'file'      => 'Le fichier :attribute ne peut pas dépasser :max kilo-octets.',
        'string'    => 'Le texte de :attribute ne peut pas dépasser :max caractères.',
        'array'     => 'Le champ :attribute ne peut pas comporter plus de :max éléments.',
    ],

    'mimes'                 => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes'             => 'Le champ :attribute doit être un fichier de type : :values.',

    'min'                   => [
        'numeric'   => 'La valeur de :attribute doit être supérieure ou égale à :min.',
        'file'      => 'Le fichier :attribute doit avoir une taille de au moins :min kilo-octets.',
        'string'    => 'Le texte :attribute doit comporter au moins :min caractères.',
        'array'     => 'Le champ :attribute doit comporter au moins :min éléments.',
    ],

    'not_in'                => 'La valeur sélectionnée pour :attribute est invalide.',
    'not_regex'             => 'Le format du champ :attribute est invalide.',
    'numeric'               => 'Le champ :attribute doit être un nombre.',
    'present'               => 'Le champ :attribute doit être présent.',
    'regex'                 => 'Le format du champ :attribute est invalide.',
    'required'              => 'Le champ :attribute est obligatoire.',
    'required_if'           => 'Le champ :attribute est obligatoire quand la valeur de :other est :value.',
    'required_if_accepted'  => 'Le champ :attribute est obligatoire quand :other est accepté.',
    'required_unless'       => "Le champ :attribute est obligatoire sauf si :other est l'une des valeurs suivantes : :values.",
    'required_with'         => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_with_all'     => 'Le champ :attribute est obligatoire quand :values sont présents.',
    'required_without'      => "Le champ :attribute est obligatoire quand :values n'est pas présent.",
    'required_without_all'  => "Le champ :attribute est obligatoire quand aucun de :values n'est présent.",
    'same'                  => 'Les champs :attribute et :other doivent être identiques.',

    'size'                  => [
        'numeric'   => 'La valeur de :attribute doit être :size.',
        'file'      => 'Le fichier :attribute doit avoir une taille de :size kilo-octets.',
        'string'    => 'Le texte :attribute doit comporter :size caractères.',
        'array'     => 'Le champ :attribute doit comporter :size éléments.',
    ],

    'starts_with'           => 'Le champ :attribute doit commencer par une des valeurs suivantes : :values.',
    'string'                => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone'              => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique'                => 'Cette valeur de :attribute est déjà utilisée.',
    'uploaded'              => "Le téléversement du fichier :attribute a échoué.",
    'url'                   => 'Le champ :attribute doit être une URL valide.',
    'uuid'                  => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Field names substituted for :attribute above. IXP Manager's form requests
    | do not define attributes() so, without these, the raw column name is
    | shown. Former also reads this array for automatic form labels - see
    | 'translate_from' in config/former.php.
    |
    */

    'attributes' => [
        'email'             => 'adresse e-mail',
        'locale'            => 'langue',
        'name'              => 'nom',
        'password'          => 'mot de passe',
        'username'          => "nom d'utilisateur",
    ],

];

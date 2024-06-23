<?php
/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute doit être accepté.',
    'accepted_if' => ':attribute doit être accepté lorsque :other est :value.',
    'active_url' => ':attribute n\'est pas un URL valide.',
    'after' => ':attribute doit être une date après :date.',
    'after_or_equal' => ':attribute doit être une date après ou égale à :date.',
    'alpha' => ':attribute ne doit contenir que des lettres.',
    'alpha_dash' => ':attribute ne doit contenir que des lettres, des nombres, des tirets et des sous-tirets.',
    'alpha_num' => ':attribute ne doit contenir que des lettres et des nombres.',
    'array' => ':attribute doit être un tableau.',
    'before' => ':attribute doit être une date avant :date.',
    'before_or_equal' => ':attribute doit être une date avant ou égale à :date.',
    'between' => [
        'numeric' => ':attribute doit être entre :min et :max.',
        'file' => ':attribute doit être entre :min et :max kilobytes.',
        'string' => ':attribute doit avoir entre :min et :max caractères.',
        'array' => ':attribute doit avoir entre :min et :max éléments.',
    ],
    'boolean' => 'Ce champ doit être vrai or faux.',
    'confirmed' => 'Le champ ne correspond pas entant que confirmation.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => ':attribute n\'est pas une date valide.',
    'date_equals' => ':attribute doit être une date égale à :date.',
    'date_format' => ':attribute ne correspond pas au format :format.',
    'declined' => ':attribute doit être refusé.',
    'declined_if' => ':attribute doit être refusé lorsque :other est :value.',
    'different' => ':attribute et :other doivent être différents.',
    'digits' => ':attribute doit avoir :digits chiffres.',
    'digits_between' => ':attribute doivent être entre :min et :max digits.',
    'dimensions' => ':attribute a des dimensions d\'image invalides.',
    'distinct' => 'Ce champ a une valeur dupliquée.',
    'email' => 'Ce champ doit être une adresse e-mail valide.',
    'ends_with' => 'Ce champ doit terminer avec l\'une des valeurs suivantes : :values.',
    'exists' => 'Le champ sélectionné est invalide.',
    'file' => 'Ce champ doit être un fichier.',
    'filled' => 'Ce champ doit avoir une valeur.',
    'gt' => [
        'numeric' => ':attribute doit être supérieur à :value.',
        'file' => ':attribute doit être supérieur à :value kilobytes.',
        'string' => ':attribute doit avoir un nombre de caractères supérieur à :value.',
        'array' => ':attribute doit avoir plus de :value éléments.',
    ],
    'gte' => [
        'numeric' => ':attribute doit être supérieur ou égal à :value.',
        'file' => ':attribute doit être supérieur ou égal à :value kilobytes.',
        'string' => ':attribute doit avoir un nombre de caractères supérieur ou égal à :value.',
        'array' => ':attribute doit avoir :value éléments ou plus.',
    ],
    'image' => ':attribute doit être une image.',
    'in' => 'Le champ sélectionné est invalide.',
    'in_array' => 'Ce champ n\'existe pas dans :other.',
    'integer' => ':attribute doit être un entier.',
    'ip' => ':attribute doit être une adresse IP valide.',
    'ipv4' => ':attribute doit être une adresse IPv4 valide.',
    'ipv6' => ':attribute doit être une adresse IPv6 valide.',
    'json' => ':attribute doit être une JSON valide.',
    'lt' => [
        'numeric' => ':attribute doit être inférieur à :value.',
        'file' => ':attribute doit être inférieur à :value kilobytes.',
        'string' => ':attribute doit avoir un nombre de caractères inférieur à :value.',
        'array' => ':attribute doit avoir moins de :value éléments.',
    ],
    'lte' => [
        'numeric' => ':attribute doit être inférieur ou égal à :value.',
        'file' => ':attribute doit être inférieur ou égal à :value kilobytes.',
        'string' => ':attribute doit avoir un nombre de caractères inférieur ou égal à :value.',
        'array' => ':attribute ne doit pas avoir plus de :value éléments.',
    ],
    'max' => [
        'numeric' => ':attribute ne doit pas être supérieur à :max.',
        'file' => ':attribute ne doit pas être supérieur à :value kilobytes.',
        'string' => ':attribute ne doit pas avoir un nombre de caractères supérieur à :value.',
        'array' => ':attribute ne doit pas avoir plus de :value éléments.',
    ],
    'mimes' => ':attribute doit être un fichier ou de type: :values.',
    'mimetypes' => ':attribute doit être un fichier ou de type: :values.',
    'min' => [
        'numeric' => ':attribute doit être au moins :min.',
        'file' => ':attribute doit être au moins :min kilobytes.',
        'string' => ':attribute doit avoir au moins :min caractères.',
        'array' => ':attribute doit avoir au moins :min éléments.',
    ],
    'multiple_of' => ':attribute doit être un multiple de :value.',
    'not_in' => 'Le champ sélectionné est invalide.',
    'not_regex' => 'Le format de :attribute est invalide.',
    'numeric' => ':attribute doit être un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'present' => 'Ce champ doit être présent.',
    'prohibited' => 'Ce champ est interdit.',
    'prohibited_if' => 'Ce champ est interdit lorsque :other est :value.',
    'prohibited_unless' => 'Ce champ est interdit à moins que :other soit dans :values.',
    'prohibits' => 'Ce champ interdit :other d\'être présent.',
    'regex' => 'Le format de :attribute est invalide.',
    'required' => 'Le champ :field_name est obligatoire.',
    'required_if' => 'Ce champ est obligatoire lorsque :other est :value.',
    'required_unless' => 'Ce champ est obligatoire à moins que :other soit dans :values.',
    'required_with' => 'Ce champ est obligatoire lorsque :values est pr&eacure;sent.',
    'required_with_all' => 'Ce champ est obligatoire lorsque :values sont pr&eacure;sents.',
    'required_without' => 'Ce champ est obligatoire lorsque :values sont absents.',
    'required_without_all' => 'Ce champ est obligatoire lorsque aucun des :values n\'est présent.',
    'same' => 'Les champs :attribute et :other doivent correspondre.',
    'size' => [
        'numeric' => ':attribute doit être :size.',
        'file' => ':attribute doit être :size kilobytes.',
        'string' => ':attribute doit avoir :size caractères.',
        'array' => ':attribute doit contenir :size éléments.',
    ],
    'starts_with' => ':attribute doit commencer avec l\'une des valeurs suivantes: :values.',
    'string' => ':attribute doit être une chaine des caractères.',
    'timezone' => ':attribute doit être un fuseau horaire valide.',
    'unique' => ':attribute a déjà été pris.',
    'uploaded' => ':attribute n\'a pas pu télécharger.',
    'url' => ':attribute doit être une URL valide.',
    'uuid' => ':attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'email' => [
            'incorrect' => 'Ecrivez une adresse e-mail valide s\'il vous plait',
            'exists' => 'L\'adresse e-mail fournie existe déjà',
        ],
        'phone' => [
            'incorrect' => 'Ecrivez un n° de téléphone valide s\'il vous plait',
            'exists' => 'Le n° de téléphone fourni existe déjà',
        ],
        'former_password' => [
            'empty' => 'Veuillez mettre votre ancien mot de passe !',
            'incorrect' => 'L\'ancien mot de passe est incorrect',
        ],
        'new_password' => [
            'empty' => 'Veuillez entrer votre nouveau mot de passe !',
            'incorrect' => 'Le nouveau mot de passe doit respecter nos conditions',
        ],
        'email_or_phone' => [
            'required' => 'L\'adresse e-mail ou le n° de téléphone doit être défini'
        ],
        'name' => [
            'exists' => 'Ce nom existe déjà'
        ],
        'code' => [
            'exists' => 'Ce code existe déjà'
        ],
        'token' => [
            'exists' => 'Ce jeton existe déjà'
        ],
        'content' => [
            'exists' => 'Ce contenu existe déjà'
        ],
        'subject' => [
            'exists' => 'Ce sujet existe déjà'
        ],
        'title' => [
            'exists' => 'Ce titre existe déjà'
        ],
        'subtitle' => [
            'exists' => 'Ce sous-titre existe déjà'
        ],
        'description' => [
            'exists' => 'This description already exists'
        ],
        'deadline' => [
            'exists' => 'Cette échéance existe déjà'
        ],
        'owner' => [
            'required' => 'A quelle entité ça appartient ?'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];

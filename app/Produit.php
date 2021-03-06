<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class produit extends Model
{
  public $table = 'produit';
  protected $fillable = [
    'nom', 'prix', 'stock', 'promotion', 'image', 'description','caracteristics'
  ];
  protected $casts = [
    'caracteristics' =>  'array'
  ];

  public function ferme()
  {
    return $this->belongsTo(ferme::class,'ferme_id');
  }

  public function commandes()
  {
    return $this->belongsToMany(commande::class, 'commande_produit', 'produit_id', 'commande_id')
      ->orderBy('created_at', 'desc')
      ->withPivot('total', 'livraison_id')
      ->withTimestamps();
  }

  public function categories()
  {
    return $this->belongsTo(categorie::class, 'categorie_id');
  }

  public function paniers()
  {
    return $this->belongsToMany(panier::class, 'produit_panier', 'produit_id', 'panier_id')
    ->withPivot('quantite')
    ->withTimestamps();
  }

  public function notes()
  {
    return $this->belongsToMany(User::class, 'produit_note', 'produit_id', 'client_id')
      ->withPivot('etoiles')
      ->using(produit_note::class)
      ->orderBy('created_at')
      ->withTimestamps();
  }
}

<?php

namespace App\Http\Controllers;

use App\panier;
use App\produit_panier;
use Illuminate\Http\Request;

class PanierController extends Controller
{
  protected $panier;

  public function __construct()
  {
    $this->panier = panier::where('ipv4', $_SERVER['REMOTE_ADDR'] ?: ($_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['HTTP_CLIENT_IP']))->first();
  }

  public function layout()
  {
    if(!$this->panier) $panier = panier::where('client_id', Auth::user()->id);
    else $panier = $this->panier;
    return $panier->produits()->count();
  }

  public function show()
  {
    if(!$this->panier) $panier = panier::where('client_id', Auth::user()->id);
    else $panier = $this->panier;
    return view('order.cart',
    [
      'panier' => $panier
    ]);
  }

  public function update(request $request, panier $panier)
  {
    foreach($request->pivot as $key => $value) dd(var_dump($value));

    foreach ($panier->produits as $produit) {
      $result = \DB::table('produit_panier')
      ->where('produit_id', $produit->id)
      ->update(['quantite' => 2]);
    }
    return redirect()->back();
  }

  public function store($produit_id)
  {
    panier::find($this->panier->id)->produits()->attach('', ['produit_id' => $produit_id]);
    return redirect()->back();
  }

  public function delete($produit_id)
  {
    panier::find($this->panier->id)->produits()->detach($produit_id);
    return redirect()->back();
  }

  //additionnal function
  public function exist($produit_id)
  {
    return produit_panier::where(['panier_id' => $this->panier->id, 'produit_id' => $produit_id])->get();
  }
}

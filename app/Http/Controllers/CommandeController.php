<?php

namespace App\Http\Controllers;

use App\commande;
use App\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Transport;
use App\Livraison;
use App\Panier;

class CommandeController extends Controller
{
  protected $time;

  public function __construct()
  {
    $this->time = $time = new \Westsworld\TimeAgo(new \Westsworld\TimeAgo\Translations\Fr());
    $this->middleware('auth');
  }

  public function show() {
    return view('order.viewMine', [
      'time' => $this->time,
      'orders' => commande::where('client_id', Auth::user()->id)->get()
    ]);
  }

  public function detail(commande $commande, $id)
  {
    $commande = commande::find($id);
    $livraison = $commande->livraison()->first();
    if ($livraison) {
      $transport = transport::find($livraison->transport_id);
      $livreur = $transport->livreur()->first();
    }
    return view('order.details',
      [
        'time'   => $this->time,
        'order' => $commande,
        'products' => $commande->products,
        'livreur' => $livreur ?? NULL
      ]);
  }

  public function order(request $request, $panier){
    $produits = panier::find($panier)->produits()->get();
    $commande = \DB::table('commande')->insertGetId([ 'description' => $request->description, 'client_id' => Auth::user()->id ]);
    $commande = commande::find($commande);
    foreach ($produits as $produit) {
      $commande->products()->attach('', ['produit_id' => $produit->id]);
    }
    return redirect()->route('orders.list');
  }

  public function edit(commande $commande, $id)
  {
    $commande = commande::find($id);
    if ($commande->livraison_id != NULL) {
      return view('order.edit', [
        'time' => $this->time,
        'order' => $commande
      ]);
    } else return dd('your can\'t change your order it is in the way');
  }

  public function delete(commande $commande, $id)
  {
    $commande = commande::find($id);
    $commande->products()->delete();
    $commande->delete(); 
    return redirect()->back();
  }
}


/**
 * Created by admin on 23/08/2018.
 */


import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
    selector: 'login',
    templateUrl: '../views/login.html'
})
export class LoginComponent implements OnInit{
     public title: string;

     constructor(
     //    private _route: AcrivatedRoute,
      //   private _router: Router
     ){
         this.title ='Componente de login';
     }
    ngOnInit(){
        console.log('login Compononant');
    }

}
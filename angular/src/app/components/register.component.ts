
/**
 * Created by admin on 23/08/2018.
 */


import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
    selector: 'register',
    templateUrl: '../views/register.html'
})
export class RegisterComponent implements OnInit{
     public title: string;

     constructor(
   //      private _route: AcrivatedRoute,
   //      private _router: Router
     ){
         this.title ='Component de register';
     }
    ngOnInit(){
        console.log('register Compononant');
    }

}
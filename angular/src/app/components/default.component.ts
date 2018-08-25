
/**
 * Created by admin on 23/08/2018.
 */


import { Component, OnInit} from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
    selector: 'default',
    templateUrl: '../views/default.html'
})
export class DefaultComponent implements OnInit{
     public title: string;

     constructor(
   private _route: ActivatedRoute,
   private _router: Router
     ){
         this.title ='HomePage';
     }
    ngOnInit(){
        console.log('defa Compononant');
    }

}
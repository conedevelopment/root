import{T as l}from"./Table.5ca6e4fc.js";import{_ as d,W as u,r as o,o as t,c as n,d as f,F as m,a as _,f as p,b as y,m as x}from"./app.1185eb56.js";import"./Actions.312fea0a.js";const g={components:{Table:l,Widget:u},props:{items:{type:Object,required:!0},filters:{type:Array,default:()=>[]},actions:{type:Array,default:()=>[]},extracts:{type:Array,default:()=>[]},widgets:{type:Array,default:()=>[]}},layout:function(a,s){return a(this.resolveDefaultLayout(),()=>s)}},b={class:"app-widget"};function k(a,s,e,v,A,B){const c=o("Widget"),i=o("Table");return t(),n("div",null,[f("div",b,[(t(!0),n(m,null,_(e.widgets,r=>(t(),y(c,x(r,{key:r.key}),null,16))),128))]),p(i,{actions:e.actions,extracts:e.extracts,filters:e.filters,items:e.items},null,8,["actions","extracts","filters","items"])])}const w=d(g,[["render",k]]);export{w as default};
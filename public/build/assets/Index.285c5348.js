import{T as l}from"./Table.6a014685.js";import{_ as u,W as d,r as n,o as t,c as r,F as m,a as f,e as p,f as _,b as y,m as g}from"./app.f181b8f7.js";import"./Actions.02b97a6a.js";const b={components:{Table:l,Widget:d},props:{items:{type:Object,required:!0},filters:{type:Array,default:()=>[]},actions:{type:Array,default:()=>[]},widgets:{type:Array,default:()=>[]},resource:{type:Object,required:!0},extract:{type:Object,required:!0}},layout:function(s,a){return s(this.resolveDefaultLayout(),()=>a)}},k={key:0,class:"app-widget"};function x(s,a,e,h,v,T){const c=n("Widget"),i=n("Table");return t(),r("div",null,[e.widgets.length>0?(t(),r("div",k,[(t(!0),r(m,null,f(e.widgets,o=>(t(),y(c,g(o,{key:o.key}),null,16))),128))])):p("",!0),_(i,{actions:e.actions,filters:e.filters,items:e.items},null,8,["actions","filters","items"])])}const w=u(b,[["render",x]]);export{w as default};
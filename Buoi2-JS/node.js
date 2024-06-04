const a = 10; // Hằng số, không thể update giá trị
let b = 20; // Có thể update giá trị
b = 50;
console.log("Gia tri b: " + b);
var c = 100;
var c = 1000; // Biến var có thể khai báo nhiều lần trong cùng 1 scope
console.log("Gia tri c: " + c);
//--------------------
let d;
console.log(d); //Undefined
let e = null;
console.log(e); //null
//--------------
const gt1 = 10;
const gt2 = "10";
console.log(gt1 == gt2); // true
console.log(gt1 === gt2); //  false
//
if (true) {
  var y = 20;
}
console.log(y);

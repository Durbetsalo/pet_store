async function addToCart(button, in_cart = false) {
  let formdata = new FormData();
  const counter = document.getElementById(
    "in_cart_" + button.getAttribute("data-product-id"),
  );
  const cart_count = document.querySelector(".cart-count");

  formdata.append("product_id", button.getAttribute("data-product-id"));
  button.disabled = true;

  let data = null;

  try {
    result = await fetch("/src/actions/product/add-to-cart.php", {
      method: "POST",
      body: formdata,
    });
    data = await result.json();

    if (data.redirect) {
      window.location.href = data.redirect;
      return data;
    }

    if (data.success == true) {
      counter.textContent = +counter.textContent + 1;
      cart_count.textContent = +cart_count.textContent + 1;
    }
    if (data.error == "stock_limit") {
      console.log(data.error == "stock_limit");
      counter.textContent = data.available;
    } else {
      button.disabled = false;
    }
    if (data.status === "created") {
      if (
        document.getElementById(
          "cart_button_" + button.getAttribute("data-product-id"),
        )
      ) {
        document.getElementById(
          "cart_button_" + button.getAttribute("data-product-id"),
        ).style.display = "none";
      }
      document.getElementById(
        "group_" + button.getAttribute("data-product-id"),
      ).style.display = "flex";
    }
  } catch (error) {
    console.log(error);
  }
  return data;
}

async function decreaseCartItem(button) {
  let res = await deleteFromCart(button);
  document.getElementById(
    "plus_button_" + button.getAttribute("data-product-id"),
  ).disabled = false;

  if (res.status === "deleted") {
    document.getElementById(
      "cart_button_" + button.getAttribute("data-product-id"),
    ).style.display = "block";
    document.getElementById(
      "group_" + button.getAttribute("data-product-id"),
    ).style.display = "none";
  }
}

// async function increaseCartItem(button) {
//   let res = await addToCart(button);
//   if (res.status === "created") {
//     document.getElementById(
//       "cart_button_" + button.getAttribute("data-product-id"),
//     ).style.display = "none";
//     document.getElementById(
//       "group_" + button.getAttribute("data-product-id"),
//     ).style.display = "flex";
//   }
// }

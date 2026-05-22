async function deleteFromCart(button, in_cart = false) {
  let formdata = new FormData();
  const counter = document.getElementById(
    "in_cart_" + button.getAttribute("data-product-id"),
  );
  const cart_count = document.querySelector(".cart-count");

  formdata.append("product_id", button.getAttribute("data-product-id"));
  button.disabled = true;

  let data = null;

  try {
    result = await fetch("/src/actions/product/delete-from-cart.php", {
      method: "POST",
      body: formdata,
    });
    data = await result.json();

    if (data.redirect) {
      window.location.href = data.redirect;
      return;
    }

    if (data.success != true) {
      // counter.textContent = oldValue;
      throw new Error(`Ошибка сервера: ${data.status}`);
    }
    document.getElementById(
      "plus_button_" + button.getAttribute("data-product-id"),
    ).disabled = false;

    if (data.status === "deleted") {
      if (
        document.getElementById(
          "cart_button_" + button.getAttribute("data-product-id"),
        )
      ) {
        document.getElementById(
          "cart_button_" + button.getAttribute("data-product-id"),
        ).style.display = "block";
      }
      document.getElementById(
        "group_" + button.getAttribute("data-product-id"),
      ).style.display = "none";
    }

    counter.textContent = counter.textContent - 1;
    cart_count.textContent = cart_count.textContent - 1;
  } catch (error) {
    console.log(error);
  }
  button.disabled = false;
  return data;
}

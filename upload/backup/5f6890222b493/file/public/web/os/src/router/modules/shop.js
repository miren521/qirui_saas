import ShopLayout from "../../layout/shop"

const shopRoutes = [
    {
        path: "/shop-*",
        name: "shop_index",
        meta: {
            module: "shop",
            backgroundColor: "#fff"
        },
        component: () => import("@/views/shop/index")
    },
    {
        path: "/shop_list",
        name: "shop_list",
        meta: {
            module: "shop"
        },
        component: () => import("@/views/shop/list")
    }
]

export default {
    path: "/shop",
    component: ShopLayout,
    redirect: "/street",
    name: "shopIndex",
    children: [...shopRoutes]
}

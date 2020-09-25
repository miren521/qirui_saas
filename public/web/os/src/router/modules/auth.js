import LoginLayout from "../../layout/login"

// 登录模块
export default {
    path: "/auth",
    component: LoginLayout,
    redirect: "/auth/login",
    alwaysShow: true,
    name: "Auth",
    children: [
        {
            path: "/login",
            name: "login",
            meta: {
                module: "login",
                backgroundColor: "#fff",
				mainCss:{
					width: "100%"
				}
            },
            component: () => import("@/views/auth/login")
        },
        {
            path: "/register",
            name: "register",
            meta: {
                module: "login",
                backgroundColor: "#fff",
				mainCss:{
					width: "100%"
				}
            },
            component: () => import("@/views/auth/register")
        },
        {
            path: "/find_pass",
            name: "find_pass",
            meta: {
                module: "login",
                backgroundColor: "#fff",
				mainCss:{
					width: "100%"
				}
            },
            component: () => import("@/views/auth/find")
        }
    ]
}

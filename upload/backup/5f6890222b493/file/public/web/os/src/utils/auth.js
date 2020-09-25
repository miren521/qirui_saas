import Cookies from 'js-cookie'

const TokenKey = 'SDrxEA%_tWW6ezd3'

export function getToken() {
    return Cookies.get(TokenKey)
}

export function setToken(token, expires = 0) {
    if (expires) return Cookies.set(TokenKey, token, { expires })
    return Cookies.set(TokenKey, token)
}

export function removeToken() {
    return Cookies.remove(TokenKey)
}

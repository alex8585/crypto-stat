declare function route(name?: string, params?: any): any;

interface TagInterface {
    name?: string,
    id?: number,
    created_at?: string
}

type ActivitiesKeysType = | 'inner' | 'url' | 'referrer';
interface ActivityType {
    page?: string,
    category?: any,
    title?: string,
    cats_shop?: any,
    action?: string,
    shop?: any,
    type?: ActivitiesKeysType,
    created_at?: string,
    id?: string,
    coupon?: any,
    user?: any,
    referrer?: string,
}
//type ActivityType = 'inner' | 'url' | '';


interface UserInterface {
    id?: number,
    created_at?: string
    name?: string,
    email?: string,
    password?: string,
    is_admin?: boolean,
    first_name?: string,
    first_name?: string,
    last_name?: string,
    username?: string,
}

interface PhotoInterface {
    categories?: [],
    imgUrl?: string,
    thumbnail?: string
    id?: number,
    created_at?: string
    name?: string,
}


type PagePropsType =
    PageProps & {
        errors: Errors & ErrorBag
        items: any,
        flash: any
    }


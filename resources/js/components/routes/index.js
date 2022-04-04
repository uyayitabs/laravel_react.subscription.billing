import async from "../components/Async";

import {
    Sliders as SlidersIcon,
    Users as UsersIcon,
    Link as LinkIcon,
    Globe as GlobeIcon,
    Star as StarIcon,
    File as FileIcon,
    CreditCard as CreditCardIcon,
    Tag as TagIcon,
    Wifi as WifiIcon,
    Inbox as InboxIcon
} from "react-feather";

// Auth
import SignIn from "../pages/auth/SignIn";
import ResetPassword from "../pages/auth/ResetPassword";
import Page404 from "../pages/auth/Page404";
import Page500 from "../pages/auth/Page500";

// Tables
import Relations from "../pages/relation/List";
import Persons from "../pages/relation/persons/List";
import Tenants from "../pages/tenant/List";
import Plans from "../pages/plan/List";
import Invoices from "../pages/invoice/List";
import Subscriptions from "../pages/subscription/List";
import Products from "../pages/product/List";
import Users from "../pages/users/List";

// 3rd Parties
import L2FiberServiceProvider from "../pages/l2fiber/index";
import M7Interface from "../pages/m7/index";

// Pages
import Profile from "../pages/pages/Profile";
import Settings from "../pages/pages/settings/index";

// Details
import RelationDetails from "../pages/relation/Details";
import AddressDetails from "../pages/relation/addresses/Details";
import TenantDetails from "../pages/tenant/Details";
import PlanDetails from "../pages/plan/Details";
import PlanLineDetails from "../pages/plan/plan_lines/Details";
import InvoiceDetails from "../pages/invoice/Details";
import SubscriptionDetails from "../pages/subscription/Details";
import SusbcriptionLineDetails from "../pages/subscription/subscription_lines/Details";

// Dashboards
const Default = async(() => import("../pages/dashboards/Default"));

// Route

const authRoute = {
    path: "/auth",
    name: "Auth",
    icon: UsersIcon,
    children: [
        {
            path: "/auth/sign-in",
            name: "Sign In",
            component: SignIn
        },
        {
            path: "/auth/reset-password",
            name: "Reset Password",
            component: ResetPassword
        },
        {
            path: "/auth/404",
            name: "404 Page",
            component: Page404
        },
        {
            path: "/auth/500",
            name: "500 Page",
            component: Page500
        }
    ]
};

const dashboardRoute = {
    path: "/",
    name: "Dashboard",
    component: Default,
    icon: SlidersIcon,
    containsHome: true,
    children: null
};

const relationRoute = {
    path: "/relations",
    name: "Relations",
    component: Relations,
    icon: LinkIcon,
    children: null
};

const relationDetailsRoute = {
    path: '/relations/:id/details',
    name: "RelationDetails",
    component: RelationDetails,
    children: null
};

const addressDetailsRoute = {
    path: '/relations/:relation/addresses/:id/details',
    name: "AddressDetails",
    component: AddressDetails,
    children: null
};

const personRoute = {
    path: "/persons",
    name: "Persons",
    component: Persons,
    children: null
};

const tenantRoute = {
    path: "/tenants",
    name: "Tenants",
    component: Tenants,
    icon: GlobeIcon,
    children: null
};

const tenantDetails = {
    path: '/tenants/:id/details',
    name: "TenantDetails",
    component: TenantDetails,
    children: null
};

const planRoute = {
    path: "/plans",
    name: "Plans",
    component: Plans,
    icon: StarIcon,
    children: null
};

const planDetailRoute = {
    path: "/plans/:id/details",
    name: "PlanDetails",
    component: PlanDetails,
    children: null
};

const PlanLineDetailsRoute = {
    path: "/plans/:id/plan-lines/:plid/details",
    name: "PlanLineDetails",
    component: PlanLineDetails,
    children: null
};

const invoiceRoute = {
    path: "/invoices",
    name: "Invoices",
    component: Invoices,
    icon: FileIcon,
    children: null
};

const invoiceDetailsRoute = {
    path: '/invoices/:id/details',
    name: "Invoice Details",
    component: InvoiceDetails,
    children: null
}

const subscriptionsRoute = {
    path: "/subscriptions",
    name: "Subscriptions",
    component: Subscriptions,
    icon: CreditCardIcon,
    children: null
};

const subscriptionDetailsRoute = {
    path: '/subscriptions/:id/details',
    name: "SubscriptionDetails",
    component: SubscriptionDetails,
    children: null
};

const SubscriptionLineDetailsRoute = {
    path: "/subscriptions/:id/subscription-lines/:slid/details",
    name: "SusbcriptionLineDetails",
    component: SusbcriptionLineDetails,
    children: null
};

const productsRoute = {
    path: "/products",
    name: "Products",
    component: Products,
    icon: TagIcon,
    children: null
};

const usersRoute = {
    path: "/users",
    name: "Users",
    component: Users,
    icon: UsersIcon,
    children: null
};

const l2FiberServicesRoute = {
    path: "/l2fiber-interface",
    name: "L2Fiber Interface",
    component: L2FiberServiceProvider,
    icon: WifiIcon,
    children: null
};

const m7InterfaceRoute = {
    path: "/m7-interface",
    name: "M7 Interface",
    component: M7Interface,
    icon: InboxIcon,
    children: null
};

const profileRoute = {
    path: "/profile",
    name: "Profile",
    component: Profile
};

const settingsRoute = {
    path: "/settings",
    name: "Settings",
    component: Settings
};

// Dashboard Routes
export const dashboard = [
    dashboardRoute,
    relationRoute,
    relationDetailsRoute,
    personRoute,
    tenantRoute,
    tenantDetails,
    planRoute,
    planDetailRoute,
    PlanLineDetailsRoute,
    invoiceRoute,
    invoiceDetailsRoute,
    subscriptionsRoute,
    subscriptionDetailsRoute,
    SubscriptionLineDetailsRoute,
    productsRoute,
    usersRoute,
    l2FiberServicesRoute,
    m7InterfaceRoute,
    addressDetailsRoute,
    profileRoute,
    settingsRoute
];

// Auth Routes
export const page = [
    authRoute
];

// Sidebar Routes displayed
export default [
    dashboardRoute,
    relationRoute,
    tenantRoute,
    planRoute,
    invoiceRoute,
    subscriptionsRoute,
    productsRoute,
    usersRoute,
    l2FiberServicesRoute,
    m7InterfaceRoute,
];

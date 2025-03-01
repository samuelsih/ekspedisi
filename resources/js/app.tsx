import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/react";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";
const queryClient = new QueryClient();

createInertiaApp({
	title: (title) => `${title} - ${appName}`,
	resolve: (name) =>
		resolvePageComponent(
			`./Pages/${name}.tsx`,
			import.meta.glob("./Pages/**/*.tsx"),
		),
	setup({ el, App, props }) {
		const appElement = (
			<QueryClientProvider client={queryClient}>
				<App {...props} />
			</QueryClientProvider>
		);

		const root = createRoot(el);
		root.render(appElement);
	},
	progress: {
		color: "#4B5563",
	},
});

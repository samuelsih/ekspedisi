import { Head } from "@inertiajs/react";
import { useForm } from "react-hook-form";
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from "zod";

const formSchema = z.object({
    customerId: z.string({
        message: "ID Customer tidak boleh kosong"
    }),
    channelId: z.string({
        message: "Channel tidak boleh kosong",
    }),
    driverId: z.string({
        message: "Driver tidak boleh kosong",
    }),
    questions: z.record(z.number()),
});

type SurveySchema = z.infer<typeof formSchema>

interface Question {
    id: string;
    title: string;
}

interface Channel {
    id: string;
    name: string;
}

interface Props {
    title: string;
    subtitle: string;
    questions: Question[];
    channels: Channel[];
}

export default function Survey({ title, subtitle, questions, channels }: Props) {
    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm<SurveySchema>({ resolver: zodResolver(formSchema) })

    return (
        <div>
            <Head title="Survey Ekspedisi JTA" />
            <h1>INI TITLE: {title}</h1>
            <h3>INI SUBTITLE: {subtitle}</h3>

            <form action="">
                <input {...register('customerId')} />
                
            </form>
        </div>
    )
}
